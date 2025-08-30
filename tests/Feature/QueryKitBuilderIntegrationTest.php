<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use LaravelQueryKit\Criteria\SortCriteria;
use LaravelQueryKit\Criteria\WhereFieldCriteria;
use LaravelQueryKit\Criteria\WithTrashedCriteria;
use LaravelQueryKit\Support\Facades\QueryKitBuilder;
use LaravelQueryKit\Tests\Stubs\Models\Post;

// Default Model stub (table: posts)

beforeEach(function () {
    // sqlite in memory
    $this->app['config']->set('database.default', 'testing');
    $this->app['config']->set('database.connections.testing', [
        'driver' => 'sqlite',
        'database' => ':memory:',
        'prefix' => '',
    ]);

    // mínimum schema
    Schema::create('posts', function (Blueprint $t) {
        $t->increments('id');
        $t->string('title');
        $t->unsignedInteger('rating');
        $t->timestamps();
        $t->softDeletes(); // to test WithTrashedCriteria
    });

    // test data
    Post::insert([
        ['title' => 'Alpha',   'rating' => 5],
        ['title' => 'Bravo',   'rating' => 3],
        ['title' => 'Charlie', 'rating' => 4],
        ['title' => 'Delta',   'rating' => 1],
    ]);
});

it('apply criteria and order with a real eloquent model', function () {
    // rating >= 3, order asc by title
    $svc = QueryKitBuilder::for(new Post);

    $svc->withCriteria(
        new WhereFieldCriteria(column: 'rating', operator: '>=', value: 3),
        (new SortCriteria(column: 'title', direction: 'asc'))->withDefaultSorts(),
    );

    $titles = $svc->toCollection()->pluck('title')->all();

    expect($titles)->toBe(['Alpha', 'Bravo', 'Charlie']); // Delta (1) it's out
});

it('include soft-deleted when WithTrashedCriteria its active', function () {
    // Delete "Bravo"
    Post::where('title', 'Bravo')->delete();

    // By default, (without withTrashed) "Bravo" cannot be returned
    $svc1 = QueryKitBuilder::for(new Post);

    $svc1->withCriteria(
        new WhereFieldCriteria('rating', '>=', 1),
        (new SortCriteria(column: 'title', direction: 'asc'))->withDefaultSorts(),
    );

    expect($svc1->toCollection()->pluck('title')->all())
        ->toBe(['Alpha', 'Charlie', 'Delta']);

    // Using WithTrashedCriteria(true) have to include "Bravo"
    $svc2 = QueryKitBuilder::for(new Post);

    $svc2->withCriteria(
        new WithTrashedCriteria(true),
        (new SortCriteria(column: 'title', direction: 'asc'))->withDefaultSorts(),
    );

    expect($svc2->toCollection()->pluck('title')->all())
        ->toBe(['Alpha', 'Bravo', 'Charlie', 'Delta']);
});
