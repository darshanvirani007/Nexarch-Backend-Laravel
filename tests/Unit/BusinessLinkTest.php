<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\BusinessLinkController;
use App\Models\BusinessLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use ReflectionMethod;
use Tests\TestCase;

class BusinessLinkTest extends TestCase
{
    public function test_it_normalizes_every_supported_business_link_type(): void
    {
        $link = new BusinessLink();

        $link->link_type = ' Email ';
        $this->assertSame('email', $link->getAttributes()['link_type']);

        $link->link_type = 'Business Suite';
        $this->assertSame('business-suite', $link->getAttributes()['link_type']);

        $link->link_type = 'custom:Project Documents';
        $this->assertSame('custom:project-documents', $link->getAttributes()['link_type']);
    }

    public function test_it_accepts_supported_types_and_rejects_unknown_identifiers(): void
    {
        $method = new ReflectionMethod(BusinessLinkController::class, 'rules');
        $rules = $method->invoke(new BusinessLinkController);
        $base = [
            'name' => 'Shortcut',
            'url' => 'https://example.com',
            'show_on_card' => true,
            'display_order' => 0,
            'is_active' => true,
        ];

        foreach (['website', 'email', 'admin', 'hosting', 'domain', 'analytics', 'business-suite', 'github', 'other', 'custom:documents'] as $type) {
            $this->assertFalse(Validator::make($base + ['link_type' => $type], $rules)->fails(), "Expected {$type} to be accepted.");
        }

        $this->assertTrue(Validator::make($base + ['link_type' => 'not/a-real-type'], $rules)->fails());
    }
}
