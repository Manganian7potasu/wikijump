<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

function find(array $array, string $field, $value)
{
    foreach ($array as $item) {
        if ($item->$field === $value) {
            return $item;
        }
    }

    throw new Error("Cannot find item in array where field $field has value $value");
}

// Eloquent doesn't format arrays for Postgres
// See https://github.com/laravel/framework/issues/27616
function format_postgres_array(string $items_json): string
{
    $items = json_decode($items_json);
    return '{' . substr(json_encode($items), 1, -1) . '}';
}

class DeepwellPage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // This hands ownership of the primary page tables to DEEPWELL

        // Remove old foreign keys
        DB::statement('ALTER TABLE file DROP CONSTRAINT file_page_id_foreign');
        DB::statement('ALTER TABLE forum_thread DROP CONSTRAINT forum_thread_page_id_foreign');
        DB::statement('ALTER TABLE front_forum_feed DROP CONSTRAINT front_forum_feed_page_id_foreign');
        DB::statement('ALTER TABLE page_connection DROP CONSTRAINT page_connection_from_page_id_fkey');
        DB::statement('ALTER TABLE page_connection DROP CONSTRAINT page_connection_to_page_id_fkey');
        DB::statement('ALTER TABLE page_connection_missing DROP CONSTRAINT page_connection_missing_from_page_id_fkey');
        DB::statement('ALTER TABLE page_link DROP CONSTRAINT page_link_page_id_fkey');
        DB::statement('ALTER TABLE page_edit_lock DROP CONSTRAINT page_edit_lock_page_id_foreign');
        DB::statement('ALTER TABLE page_rate_vote DROP CONSTRAINT page_rate_vote_page_id_foreign');
        DB::statement('ALTER TABLE watched_page DROP CONSTRAINT watched_page_page_id_foreign');

        // Drop old tables
        Schema::drop('page');
        Schema::drop('page_revision');
        Schema::drop('page_metadata');

        // Create new tables
        DB::statement("
            CREATE TABLE page (
                page_id BIGSERIAL PRIMARY KEY,
                created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now(),
                updated_at TIMESTAMP WITH TIME ZONE,
                deleted_at TIMESTAMP WITH TIME ZONE,
                site_id BIGINT NOT NULL REFERENCES site(site_id),
                page_category_id BIGINT NOT NULL REFERENCES category(category_id),
                slug TEXT NOT NULL,
                discussion_thread_id BIGINT REFERENCES forum_thread(thread_id),

                UNIQUE (site_id, slug)
            )
        ");

        DB::statement("
            CREATE TABLE page_revision (
                revision_id BIGSERIAL PRIMARY KEY,
                created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now(),
                revision_number INT NOT NULL,
                page_id BIGINT NOT NULL REFERENCES page(page_id),
                site_id BIGINT NOT NULL REFERENCES site(site_id),
                user_id BIGINT NOT NULL REFERENCES users(id),
                wikitext_hash BYTEA NOT NULL REFERENCES text(hash),
                compiled_hash BYTEA REFERENCES text(hash),
                compiled_at TIMESTAMP WITH TIME ZONE,
                compiled_generator TEXT,
                comments TEXT NOT NULL,
                comments_edited_at TIMESTAMP WITH TIME ZONE,
                comments_edited_by BIGINT REFERENCES users(id),
                hidden TEXT[] NOT NULL DEFAULT '{}', -- List of fields to be hidden/suppressed
                title TEXT NOT NULL,
                alt_title TEXT,
                slug TEXT NOT NULL,
                tags TEXT[] NOT NULL DEFAULT '{}', -- Should be sorted and deduplicated before insertion
                metadata JSONB NOT NULL DEFAULT '{}', -- Customizable metadata. Currently unused.

                -- Ensure all compiled fields are null, or all are non-null
                CHECK ((compiled_hash IS NULL) = (compiled_at IS NULL)),
                CHECK ((compiled_at IS NULL) = (compiled_generator IS NULL)),

                -- Ensure both comments_edited fields are null, or both are non-null
                CHECK ((comments_edited_at IS NULL) = (comments_edited_by IS NULL)),

                UNIQUE (page_id, site_id, revision_number)
            )
        ");

        DB::statement("
            CREATE TABLE page_parent (
                parent_page_id BIGINT REFERENCES page(page_id),
                child_page_id BIGINT REFERENCES page(page_id),
                created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now(),

                PRIMARY KEY (parent_page_id, child_page_id)
            )
        ");

        DB::statement("
            CREATE TABLE page_attribution (
                page_id BIGINT REFERENCES page(page_id),
                user_id BIGINT REFERENCES users(id),
                -- Text enum describing the kind of attribution
                -- Currently synced to Crom: 'author', 'rewrite', 'translator', 'maintainer'
                attribution_type TEXT NOT NULL,
                attribution_date DATE NOT NULL,
                created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now(),

                PRIMARY KEY (page_id, user_id, attribution_type, attribution_date)
            )
        ");

        DB::statement("
            CREATE TABLE page_lock (
                page_lock_id BIGSERIAL PRIMARY KEY,
                created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now(),
                updated_at TIMESTAMP WITH TIME ZONE,
                deleted_at TIMESTAMP WITH TIME ZONE,
                -- Text enum describing what kind of lock (e.g. authors only, staff only)
                -- Currently the only value is 'wikidot' (meaning mods+ only)
                lock_type TEXT NOT NULL,
                page_id BIGINT NOT NULL REFERENCES page(page_id),
                user_id BIGINT NOT NULL REFERENCES users(id),
                reason TEXT NOT NULL,

                UNIQUE (page_id, deleted_at)
            )
        ");

        // Add new foreign keys
        DB::statement('ALTER TABLE file ADD FOREIGN KEY page_id REFERENCES page(page_id)');
        DB::statement('ALTER TABLE forum_thread ADD FOREIGN KEY page_id REFERENCES page(page_id)');
        DB::statement('ALTER TABLE front_forum_feed ADD FOREIGN KEY page_id REFERENCES page(page_id)');
        DB::statement('ALTER TABLE page_connection ADD FOREIGN KEY from_page_id REFERENCES page(page_id)');
        DB::statement('ALTER TABLE page_connection ADD FOREIGN KEY to_page_id REFERENCES page(page_id)');
        DB::statement('ALTER TABLE page_connection_missing ADD FOREIGN KEY from_page_id REFERENCES page(page_id)');
        DB::statement('ALTER TABLE page_link ADD FOREIGN KEY page_id REFERENCES page(page_id)');
        DB::statement('ALTER TABLE page_edit_lock ADD FOREIGN KEY page_id REFERENCES page(page_id)');
        DB::statement('ALTER TABLE page_rate_vote ADD FOREIGN KEY page_id REFERENCES page(page_id)');
        DB::statement('ALTER TABLE watched_page ADD FOREIGN KEY page_id REFERENCES page(page_id)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('page', function (Blueprint $table) {
            $table->id('page_id')->startingValue(53);
            $table->unsignedInteger('site_id')->nullable()->index();
            $table->unsignedInteger('category_id')->nullable()->index();
            $table->unsignedInteger('parent_page_id')->nullable()->index();
            $table->unsignedInteger('revision_id')->nullable()->index();
            $table->unsignedInteger('source_id')->nullable();
            $table->unsignedInteger('metadata_id')->nullable();
            $table->unsignedInteger('revision_number')->default(0);
            $table->string('title', 256)->nullable();
            $table->string('unix_name', 256)->nullable()->index();
            $table->timestamp('date_created')->nullable();
            $table->timestamp('date_last_edited')->nullable();
            $table->unsignedInteger('last_edit_user_id')->nullable();
            $table->string('last_edit_user_string', 80)->nullable();
            $table->unsignedInteger('thread_id')->nullable();
            $table->unsignedInteger('owner_user_id')->nullable();
            $table->boolean('blocked')->default(false);
            $table->integer('rate')->default(0);

            $table->unique(['site_id', 'unix_name']);
        });

        Schema::create('page_revision', function (Blueprint $table) {
            $table->id('revision_id')->startingValue(64);
            $table->unsignedInteger('page_id')->nullable()->index();
            $table->unsignedInteger('source_id')->nullable();
            $table->unsignedInteger('metadata_id')->nullable();
            $table->string('flags', 100)->nullable();
            $table->boolean('flag_text')->default(false);
            $table->boolean('flag_title')->default(false);
            $table->boolean('flag_file')->default(false);
            $table->boolean('flag_rename')->default(false);
            $table->boolean('flag_meta')->default(false);
            $table->boolean('flag_new')->default(false);
            $table->unsignedInteger('since_full_source')->nullable();
            $table->boolean('diff_source')->default(false);
            $table->unsignedInteger('revision_number')->nullable();
            $table->timestamp('date_last_edited')->nullable();
            $table->unsignedInteger('user_id')->nullable()->index();
            $table->string('user_string', 80)->nullable();
            $table->string('comments', 200000)->nullable();
            $table->boolean('flag_new_site')->default(false);
            $table->unsignedInteger('site_id')->nullable()->index();
        });

        Schema::create('page_metadata', function (Blueprint $table) {
            $table->id('metadata_id')->startingValue(57);
            $table->unsignedInteger('parent_page_id')->nullable();
            $table->string('title', 256)->nullable();
            $table->string('unix_name', 80)->nullable();
            $table->unsignedInteger('owner_user_id')->nullable();
        });
    }
}