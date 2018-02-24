<?php
namespace Astral\Lib;

use Zttp\Zttp;
use Astral\Exceptions\InvalidAccessTokenException;

class GitHubClient
{
  protected $endpoint;
  protected $token;

  public function __construct($token)
  {
    if (!$token) {
      throw new MissingAccessTokenException;
    }

    $this->endpoint = 'https://api.github.com/graphql';
    $this->token = $token;
  }

  public function fetchStars($cursor = null, $perPage = 100)
  {
    $cursorString = $cursor ? 'after:"' . $cursor . '"' : 'after: null';
    $query = <<<GQL
    query {
      viewer {
        starredRepositories(first: {$perPage}, orderBy: {field: STARRED_AT, direction: DESC},  {$cursorString}) {
          totalCount
          edges {
            node {
              id
              nameWithOwner
              description
              url
              databaseId
              primaryLanguage {
                name
              }
              stargazers {
                totalCount
              }
              forkCount
            }
            cursor
          }
          pageInfo {
            endCursor
            hasNextPage
          }
        }
      }
    }
GQL;

    $response = Zttp::withHeaders([
      'Authorization' => 'Bearer ' . $this->token,
      'Content-Type' => 'application/json',
    ])->post($this->endpoint, [
      'query' => $query,
      'variables' => '',
    ]);

    if ($response->getStatusCode() == 401) {
      throw new InvalidAccessTokenException;
    }

    return $response->json()['data']['viewer']['starredRepositories'];
  }
}