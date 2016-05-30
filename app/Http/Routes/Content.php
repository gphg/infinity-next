<?php

/**
 * Mostly static root-level content.
 *
 * @category   Routes
 *
 * @author     Joshua Moon <josh@jaw.sh>
 * @copyright  2016 Infinity Next Development Group
 * @license    http://www.gnu.org/licenses/agpl-3.0.en.html AGPL3
 *
 * @since      0.6.0
 */

Route::get('boards.html', [
    'as'   => 'boardlist',
    'uses' => 'BoardlistController@getIndex',
]);

Route::get('overboard.html', [
    'as'   => 'overboard',
    'uses' => 'MultiboardController@getOverboard',
]);

/**
 * Static Pages
 */
Route::group(['middleware' => \App\Http\Middleware\BoardAmbivilance::class,], function() {
    // site static page
    Route::get('{page_title}.html', ['as' => 'page', 'uses' => 'PageController@show',]);

    // board static page
    Route::get('{board}/{page_title}.html', ['as' => 'board.page', 'uses' => 'PageController@show',]);
});

/*
if (env('CONTRIB_ENABLED', false)) {
    Route::get('contribute', 'PageController@getContribute');
    Route::get('contribute.json', 'API\PageController@getContribute');
}
*/

/**
 * Front page controller
 * Always keep dead last.
 */
Route::get('/', [
    'as'   => 'home',
    'uses' => 'WelcomeController@getIndex',
]);