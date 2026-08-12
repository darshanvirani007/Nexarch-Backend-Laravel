<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\BusinessController;
use App\Http\Controllers\Api\BusinessSocialLinkController;
use App\Models\Business;
use App\Models\BusinessSocialLink;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class BusinessSocialLinkTest extends TestCase
{
    private const USER_ID = '11111111-1111-4111-8111-111111111111';

    private const OTHER_USER_ID = '22222222-2222-4222-8222-222222222222';

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');

        Schema::create('businesses', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_archived')->default(false);
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });
        Schema::create('business_social_links', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('business_id');
            $table->string('platform');
            $table->string('username')->nullable();
            $table->text('url');
            $table->boolean('show_on_card')->default(true);
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('business_links', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('business_id');
            $table->string('link_type');
            $table->string('name');
            $table->text('url');
            $table->boolean('show_on_card')->default(true);
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('business_development_keys', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('business_id');
            $table->uuid('vault_secret_id')->nullable();
            $table->string('name')->nullable();
            $table->string('key_type')->nullable();
            $table->string('environment')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('business_notes', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('business_id')->unique();
            $table->text('content')->nullable();
            $table->timestamps();
        });
        Schema::create('website_checks', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('business_id');
            $table->string('status')->nullable();
            $table->integer('http_status_code')->nullable();
            $table->integer('response_time_ms')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('checked_at')->nullable();
        });
    }

    public function test_create_persists_false_and_returns_a_json_boolean(): void
    {
        $business = $this->business();
        $response = $this->controller()->store($this->request('POST', [
            'platform' => 'linkedin',
            'username' => 'nexarch',
            'url' => 'https://linkedin.com/company/nexarch',
            'show_on_card' => false,
        ]), $business->id);

        $this->assertSame(201, $response->getStatusCode());
        $this->assertFalse($response->getData(true)['show_on_card']);
        $this->assertFalse(BusinessSocialLink::firstOrFail()->show_on_card);
    }

    public function test_create_defaults_omitted_visibility_to_true(): void
    {
        $business = $this->business();
        $response = $this->controller()->store($this->request('POST', [
            'platform' => 'github',
            'url' => 'https://github.com/nexarch',
        ]), $business->id);

        $this->assertTrue($response->getData(true)['show_on_card']);
        $this->assertTrue(BusinessSocialLink::firstOrFail()->show_on_card);
    }

    public function test_visibility_can_be_updated_in_both_directions(): void
    {
        $business = $this->business();
        $social = $this->social($business, true);

        $falseResponse = $this->controller()->update($this->request('PUT', ['show_on_card' => false]), $business->id, $social->id);
        $this->assertFalse($falseResponse->getData(true)['show_on_card']);

        $trueResponse = $this->controller()->update($this->request('PUT', ['show_on_card' => true]), $business->id, $social->id);
        $this->assertTrue($trueResponse->getData(true)['show_on_card']);
    }

    public function test_platform_username_and_url_can_be_updated_individually(): void
    {
        $business = $this->business();
        $social = $this->social($business, true);

        $platform = $this->controller()->update(
            $this->request('PUT', ['platform' => 'github']),
            $business->id,
            $social->id,
        )->getData(true);
        $this->assertSame('github', $platform['platform']);

        $username = $this->controller()->update(
            $this->request('PUT', ['username' => 'nexarchhq']),
            $business->id,
            $social->id,
        )->getData(true);
        $this->assertSame('nexarchhq', $username['username']);

        $url = $this->controller()->update(
            $this->request('PUT', ['url' => 'https://github.com/nexarchhq']),
            $business->id,
            $social->id,
        )->getData(true);
        $this->assertSame('https://github.com/nexarchhq', $url['url']);
    }

    public function test_updating_another_field_does_not_reset_visibility(): void
    {
        $business = $this->business();
        $social = $this->social($business, false);

        $response = $this->controller()->update($this->request('PUT', ['username' => 'updated']), $business->id, $social->id);

        $this->assertSame('updated', $response->getData(true)['username']);
        $this->assertFalse($response->getData(true)['show_on_card']);
    }

    public function test_business_list_and_detail_responses_include_boolean_visibility(): void
    {
        $business = $this->business();
        $this->social($business, false);
        $controller = new BusinessController;

        $list = $controller->index($this->request('GET'))->getData(true);
        $detail = $controller->show($this->request('GET'), $business->id)->getData(true);

        $this->assertFalse($list[0]['social_links'][0]['show_on_card']);
        $this->assertFalse($detail['social_links'][0]['show_on_card']);
    }

    public function test_invalid_visibility_fails_validation(): void
    {
        $this->expectException(ValidationException::class);
        $this->controller()->store($this->request('POST', [
            'platform' => 'linkedin',
            'url' => 'https://linkedin.com/company/nexarch',
            'show_on_card' => 'definitely',
        ]), $this->business()->id);
    }

    public function test_invalid_update_url_fails_validation(): void
    {
        $business = $this->business();
        $social = $this->social($business, true);

        $this->expectException(ValidationException::class);
        $this->controller()->update(
            $this->request('PUT', ['url' => 'javascript:alert(1)']),
            $business->id,
            $social->id,
        );
    }

    public function test_another_user_cannot_update_a_social_link(): void
    {
        $business = $this->business(self::OTHER_USER_ID);
        $social = $this->social($business, true, self::OTHER_USER_ID);

        $this->expectException(ModelNotFoundException::class);
        $this->controller()->update($this->request('PUT', ['show_on_card' => false]), $business->id, $social->id);
    }

    public function test_another_user_cannot_delete_a_social_link(): void
    {
        $business = $this->business(self::OTHER_USER_ID);
        $social = $this->social($business, true, self::OTHER_USER_ID);

        $this->expectException(ModelNotFoundException::class);
        $this->controller()->destroy($this->request('DELETE'), $business->id, $social->id);
    }

    public function test_social_link_cannot_be_updated_through_the_wrong_business(): void
    {
        $correctBusiness = $this->business();
        $wrongBusiness = $this->business();
        $social = $this->social($correctBusiness, true);

        $this->expectException(ModelNotFoundException::class);
        $this->controller()->update(
            $this->request('PUT', ['username' => 'intruder']),
            $wrongBusiness->id,
            $social->id,
        );
    }

    public function test_business_detail_reflects_social_link_edits_and_deletion(): void
    {
        $business = $this->business();
        $social = $this->social($business, true);
        $request = $this->request('GET');

        $this->controller()->update(
            $this->request('PUT', ['platform' => 'github', 'show_on_card' => false]),
            $business->id,
            $social->id,
        );

        $afterEdit = (new BusinessController)->show($request, $business->id)->getData(true);
        $this->assertSame('github', $afterEdit['social_links'][0]['platform']);
        $this->assertFalse($afterEdit['social_links'][0]['show_on_card']);

        $response = $this->controller()->destroy($this->request('DELETE'), $business->id, $social->id);
        $this->assertSame(204, $response->getStatusCode());
        $this->assertDatabaseMissing('business_social_links', ['id' => $social->id]);

        $afterDelete = (new BusinessController)->show($request, $business->id)->getData(true);
        $this->assertSame([], $afterDelete['social_links']);
    }

    public function test_existing_create_update_and_delete_behaviour_remains_available(): void
    {
        $business = $this->business();
        $created = $this->controller()->store($this->request('POST', [
            'platform' => 'linkedin',
            'username' => 'nexarch',
            'url' => 'https://linkedin.com/company/nexarch',
        ]), $business->id)->getData(true);

        $updated = $this->controller()->update($this->request('PUT', ['username' => 'nexarchhq']), $business->id, $created['id']);
        $this->assertSame('nexarchhq', $updated->getData(true)['username']);

        $deleted = $this->controller()->destroy($this->request('DELETE'), $business->id, $created['id']);
        $this->assertSame(204, $deleted->getStatusCode());
        $this->assertDatabaseMissing('business_social_links', ['id' => $created['id']]);
    }

    private function controller(): BusinessSocialLinkController
    {
        return new BusinessSocialLinkController;
    }

    private function request(string $method, array $data = [], string $userId = self::USER_ID): Request
    {
        $request = Request::create('/', $method, $data);
        $request->attributes->set('user_id', $userId);

        return $request;
    }

    private function business(string $userId = self::USER_ID): Business
    {
        return Business::create([
            'user_id' => $userId,
            'name' => 'Nexarch',
            'description' => 'Workspace',
            'is_archived' => false,
            'display_order' => 0,
        ]);
    }

    private function social(Business $business, bool $showOnCard, string $userId = self::USER_ID): BusinessSocialLink
    {
        return $business->socialLinks()->create([
            'user_id' => $userId,
            'platform' => 'linkedin',
            'username' => 'nexarch',
            'url' => 'https://linkedin.com/company/nexarch',
            'show_on_card' => $showOnCard,
            'display_order' => 0,
            'is_active' => true,
        ]);
    }
}
