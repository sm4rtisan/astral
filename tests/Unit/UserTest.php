<?php

namespace Tests\Unit;

use Tests\TestCase;
use Astral\Models\User;
use Tests\Stubs\GitHubUser;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp()
    {
        parent::setUp();

        $this->user = create('Astral\Models\User');
    }

    /** @test */
    public function it_has_associated_stars()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->user->stars);
    }

    /** @test */
    public function it_has_associated_tags()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->user->tags);
    }

    /** @test */
    public function it_can_map_a_github_user_object()
    {
        $githubUser = new GitHubUser();

        $this->user->mapGitHubUser($githubUser);

        $this->assertEquals(
            $githubUser->getNickname(),
            $this->user->username
        );

        $this->assertEquals(
            $githubUser->getId(),
            $this->user->github_id
        );

        $this->assertEquals(
            $githubUser->getName(),
            $this->user->name
        );

        $this->assertEquals(
            $githubUser->getAvatar(),
            $this->user->avatar_url
        );

        $this->assertEquals(
            $githubUser->token,
            $this->user->access_token
        );
    }

    /** @test */
    public function it_can_fetch_the_user_specific_cache_key_for_stars()
    {
        $id = $this->user->id;

        $this->assertEquals(
            "user_{$id}.github_stars",
            $this->user->starsCacheKey()
        );
    }
}
