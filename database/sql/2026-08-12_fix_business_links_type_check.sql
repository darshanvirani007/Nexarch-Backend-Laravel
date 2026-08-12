-- Run this once in Supabase Dashboard -> SQL Editor.
-- It changes only the business_links link-type check. It does not delete rows,
-- disable RLS, change ownership policies, or recreate the table.

begin;

alter table public.business_links
    drop constraint if exists business_links_type_check;

alter table public.business_links
    add constraint business_links_type_check
    check (
        link_type in (
            'website',
            'email',
            'admin',
            'hosting',
            'domain',
            'analytics',
            'business-suite',
            'github',
            'other'
        )
        or link_type ~ '^custom:[a-z0-9][a-z0-9-]*$'
    );

commit;
