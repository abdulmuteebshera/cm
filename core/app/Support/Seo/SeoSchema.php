<?php

namespace App\Support\Seo;

use Illuminate\Support\Facades\Schema;

class SeoSchema
{
    protected static array $columns = [];

    public static function columns(string $table): array
    {
        if (!isset(self::$columns[$table])) {
            self::$columns[$table] = Schema::hasTable($table) ? Schema::getColumnListing($table) : [];
        }

        return self::$columns[$table];
    }

    public static function has(string $table, string $column): bool
    {
        return in_array($column, self::columns($table), true);
    }

    public static function first(string $table, array $candidates, string $fallback = 'created_at'): string
    {
        foreach ($candidates as $column) {
            if (self::has($table, $column)) {
                return $column;
            }
        }

        return $fallback;
    }

    public static function only(string $table, array $data): array
    {
        $allowed = array_flip(self::columns($table));
        return array_intersect_key($data, $allowed);
    }

    public static function sessionStart(): string
    {
        return self::first('seo_visitor_sessions', ['started_at', 'first_seen_at', 'created_at']);
    }

    public static function lastSeen(): string
    {
        return self::first('seo_visitor_sessions', ['last_seen_at', 'updated_at']);
    }

    public static function visitor(string $table = 'seo_visitor_sessions'): string
    {
        return self::first($table, ['visitor_key', 'visitor_id']);
    }

    public static function ip(): string
    {
        return self::first('seo_visitor_sessions', ['ip_address', 'ip']);
    }

    public static function landing(): string
    {
        return self::first('seo_visitor_sessions', ['landing_path', 'landing_page']);
    }

    public static function exitPage(): string
    {
        return self::first('seo_visitor_sessions', ['exit_path', 'exit_page']);
    }

    public static function pageCount(): string
    {
        return self::first('seo_visitor_sessions', ['page_count', 'page_views']);
    }

    public static function channel(): string
    {
        return self::first('seo_visitor_sessions', ['channel', 'source']);
    }

    public static function engage(): string
    {
        return self::first('seo_visitor_sessions', ['engaged_seconds', 'duration_seconds']);
    }

    public static function medium(): string
    {
        return self::first('seo_visitor_sessions', ['utm_medium', 'medium']);
    }

    public static function campaign(): string
    {
        return self::first('seo_visitor_sessions', ['utm_campaign', 'campaign']);
    }

    public static function sessionFk(string $table = 'seo_page_views'): string
    {
        return self::first($table, ['seo_visitor_session_id', 'session_id'], 'session_id');
    }

    public static function pageViewFk(): string
    {
        return self::first('seo_events', ['seo_page_view_id', 'page_view_id']);
    }

    public static function pageTitle(): string
    {
        return self::first('seo_page_views', ['page_title', 'title']);
    }

    public static function leftAt(): string
    {
        return self::first('seo_page_views', ['left_at', 'exited_at']);
    }

    public static function href(): string
    {
        return self::first('seo_events', ['href', 'target_url']);
    }

    public static function eventTime(): string
    {
        return self::first('seo_events', ['occurred_at', 'created_at']);
    }
}
