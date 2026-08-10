<?php

namespace Tests\Unit;

use App\Models\MyLink;
use PHPUnit\Framework\TestCase;

class MyLinkTest extends TestCase
{
    public function test_it_normalizes_display_link_types_for_the_database_constraint(): void
    {
        $link = new MyLink();

        $link->link_type = 'GitHub';
        $this->assertSame('github', $link->getAttributes()['link_type']);

        $link->link_type = ' YouTube ';
        $this->assertSame('youtube', $link->getAttributes()['link_type']);
    }
}
