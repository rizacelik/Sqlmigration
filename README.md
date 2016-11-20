# 
# Laravel 5* Sql Migrations and Seedings

Converts database all table sql to migrations and Seeding.


## Usage

### Step 1: Install Through Composer

```
composer require Sqlmigration\MigrationsSeedings:"dev-master"
```

### Step 2: Add the Service Provider


Open `config/app.php` and, add to `Sqlmigration\MigrationsSeedings\SqlMigrationServiceProvider::class`

```
'providers' => [
     . . .
     Sqlmigration\MigrationsSeedings\SqlMigrationServiceProvider::class,
],
```


### Step 3: Run Artisan!

~~~
php artisan sql:migration
~~~

### Options: Ejects table command

~~~
php artisan sql:migration --eject="table1,table2,table3"
~~~


## Example Migration and Seeding


```php
<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
//
// NOTE Migration Created: 2016-11-20 08:21:51
// --------------------------------------------------
 
class CreateKabakDatabase  extends Migration{
//
// NOTE - Make changes to the database.
// --------------------------------------------------
 
public function up()
{

//
// NOTE -- blog_categories
// --------------------------------------------------
 
if (!Schema::hasTable('blog_categories')) {
Schema::create('blog_categories', function(Blueprint $table) {
	$table->increments('id')->unsigned();
	$table->string('name', 255);
	$table->string('slug', 255)->unique();
	$table->string('description', 255)->nullable();
 });

DB::table('blog_categories')->insert(
array (
  0 => 
  array (
    'id' => 1,
    'name' => 'Test category - 1',
    'slug' => 'test-category-1',
    'description' => 'Test category - 1 Meta Desc',
  ),
  1 => 
  array (
    'id' => 2,
    'name' => 'Test category - 2',
    'slug' => 'test-category-2',
    'description' => 'Test category - 2 Meta Desc',
  ),
  2 => 
  array (
    'id' => 3,
    'name' => 'Test category - 3',
    'slug' => 'test-category-3',
    'description' => 'Test category - 3 Meta Desc',
  ),
));

}else{
   echo "blog_categories table already exist.\r\n";
}


//
// NOTE -- blog_comments
// --------------------------------------------------
 
if (!Schema::hasTable('blog_comments')) {
Schema::create('blog_comments', function(Blueprint $table) {
	$table->increments('id')->unsigned();
	$table->string('commentext', 255);
	$table->unsignedInteger('post_id');
	$table->string('ip', 15);
	$table->enum('status', array('pending','publish','spam'))->default('pending');
	$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
	$table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
 });

DB::table('blog_comments')->insert(
array (
  0 => 
  array (
    'id' => 1,
    'commentext' => 'Client comment text ',
    'post_id' => 2,
    'ip' => '127.0.0.1',
    'status' => 'pending',
    'created_at' => '2016-11-05 16:22:53',
    'updated_at' => '2016-11-05 16:22:53',
  ),
  1 => 
  array (
    'id' => 2,
    'commentext' => 'Yeni bir yorum',
    'post_id' => 1,
    'ip' => '127.0.0.1',
    'status' => 'pending',
    'created_at' => '2016-11-15 19:47:36',
    'updated_at' => '2016-11-15 19:47:36',
  ),
));

}else{
   echo "blog_comments table already exist.\r\n";
}


//
// NOTE -- blog_post_tag
// --------------------------------------------------
 
if (!Schema::hasTable('blog_post_tag')) {
Schema::create('blog_post_tag', function(Blueprint $table) {
	$table->increments('id')->unsigned();
	$table->unsignedInteger('tag_id');
	$table->unsignedInteger('post_id');
 });

DB::table('blog_post_tag')->insert(
array (
  0 => 
  array (
    'id' => 1,
    'tag_id' => 1,
    'post_id' => 1,
  ),
  1 => 
  array (
    'id' => 2,
    'tag_id' => 2,
    'post_id' => 2,
  ),
  2 => 
  array (
    'id' => 3,
    'tag_id' => 3,
    'post_id' => 3,
  ),
));

}else{
   echo "blog_post_tag table already exist.\r\n";
}


//
// NOTE -- blog_posts
// --------------------------------------------------
 
if (!Schema::hasTable('blog_posts')) {
Schema::create('blog_posts', function(Blueprint $table) {
	$table->increments('id')->unsigned();
	$table->unsignedInteger('category_id');
	$table->string('title', 255);
	$table->text('summary');
	$table->text('content');
	$table->string('slug', 255)->unique();
	$table->enum('status', array('draft','publish'))->default('publish');
	$table->boolean('comments');
	$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
	$table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
 });

DB::table('blog_posts')->insert(
array (
  0 => 
  array (
    'id' => 1,
    'category_id' => 1,
    'title' => 'Test information - 1',
    'summary' => 'Test information - summary - 1',
    'content' => 'Lorem ipsum dolor sit \'amet\', consectetur adipiscing elit. Curabitur varius eros ut ornare tempus. Cras a ligula lectus. Pellentesque eget tempor arcu. Proin nisl mi, auctor sit amet ornare vitae, egestas sit amet ante. Phasellus sit amet lobortis risus. Nam consectetur nisi consectetur aliquet condimentum. Morbi eu lacus in neque bibendum ultricies vel in risus.',
    'slug' => 'test-information-1',
    'status' => 'publish',
    'comments' => 1,
    'created_at' => '2016-11-05 16:22:52',
    'updated_at' => '2016-11-18 23:44:41',
  ),
  1 => 
  array (
    'id' => 2,
    'category_id' => 2,
    'title' => 'Test information - 2',
    'summary' => 'Test information - summary - 2',
    'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur varius eros ut ornare tempus. Cras a ligula lectus. Pellentesque eget tempor arcu. Proin nisl mi, auctor sit amet ornare vitae, egestas sit amet ante. Phasellus sit amet lobortis risus. Nam consectetur nisi consectetur aliquet condimentum. Morbi eu lacus in neque bibendum ultricies vel in risus.',
    'slug' => 'test-information-2',
    'status' => 'publish',
    'comments' => 0,
    'created_at' => '2016-11-05 16:22:52',
    'updated_at' => '2016-11-05 16:22:52',
  ),
  2 => 
  array (
    'id' => 3,
    'category_id' => 3,
    'title' => 'Test information - 3',
    'summary' => 'Test information - summary - 3',
    'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur varius eros ut ornare tempus. Cras a ligula lectus. Pellentesque eget tempor arcu. Proin nisl mi, auctor sit amet ornare vitae, egestas sit amet ante. Phasellus sit amet lobortis risus. Nam consectetur nisi consectetur aliquet condimentum. Morbi eu lacus in neque bibendum ultricies vel in risus.',
    'slug' => 'test-information-3',
    'status' => 'publish',
    'comments' => 0,
    'created_at' => '2016-11-05 16:22:52',
    'updated_at' => '2016-11-16 09:51:03',
  ),
));

}else{
   echo "blog_posts table already exist.\r\n";
}


//
// NOTE -- blog_tags
// --------------------------------------------------
 
if (!Schema::hasTable('blog_tags')) {
Schema::create('blog_tags', function(Blueprint $table) {
	$table->increments('id')->unsigned();
	$table->string('name', 255);
	$table->string('slug', 255)->unique();
 });

DB::table('blog_tags')->insert(
array (
  0 => 
  array (
    'id' => 1,
    'name' => 'client',
    'slug' => 'comment-text1',
  ),
  1 => 
  array (
    'id' => 2,
    'name' => 'test',
    'slug' => 'comment-text2',
  ),
  2 => 
  array (
    'id' => 3,
    'name' => 'summary',
    'slug' => 'comment-text3',
  ),
));

}else{
   echo "blog_tags table already exist.\r\n";
}


//
// NOTE -- password_resets
// --------------------------------------------------
 
if (!Schema::hasTable('password_resets')) {
Schema::create('password_resets', function(Blueprint $table) {
	$table->string('email', 255);
	$table->string('token', 255);
	$table->timestamp('created_at')->nullable();
 });


}else{
   echo "password_resets table already exist.\r\n";
}


//
// NOTE -- users
// --------------------------------------------------
 
if (!Schema::hasTable('users')) {
Schema::create('users', function(Blueprint $table) {
	$table->increments('id')->unsigned();
	$table->string('name', 255);
	$table->string('email', 255)->unique();
	$table->string('password', 255);
	$table->string('remember_token', 100)->nullable();
	$table->timestamp('created_at')->nullable();
	$table->timestamp('updated_at')->nullable();
 });


}else{
   echo "users table already exist.\r\n";
}


//
// NOTE -- blog_comments_foreign
// --------------------------------------------------
 
Schema::table('blog_comments', function(Blueprint $table) {
  $table->foreign('post_id')
	->references('id')
	->on('blog_posts')
	->onDelete('CASCADE')
	->onUpdate('RESTRICT');
 });


//
// NOTE -- blog_post_tag_foreign
// --------------------------------------------------
 
Schema::table('blog_post_tag', function(Blueprint $table) {
  $table->foreign('post_id')
	->references('id')
	->on('blog_posts')
	->onDelete('CASCADE')
	->onUpdate('RESTRICT');
  $table->foreign('tag_id')
	->references('id')
	->on('blog_tags')
	->onDelete('CASCADE')
	->onUpdate('RESTRICT');
 });


//
// NOTE -- blog_posts_foreign
// --------------------------------------------------
 
Schema::table('blog_posts', function(Blueprint $table) {
  $table->foreign('category_id')
	->references('id')
	->on('blog_categories')
	->onDelete('CASCADE')
	->onUpdate('RESTRICT');
 });



}
 
//
// NOTE - Revert the changes to the database.
// --------------------------------------------------
 
public function down()
{

Schema::drop('blog_categories');
Schema::drop('blog_comments');
Schema::drop('blog_post_tag');
Schema::drop('blog_posts');
Schema::drop('blog_tags');
Schema::drop('password_resets');
Schema::drop('users');

}
}
```
