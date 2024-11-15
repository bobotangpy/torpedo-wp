<?php


namespace Torpedo\Wp\ViewElements\Interfaces;


use Torpedo\Wp\Posts\CardListable;
use Torpedo\Wp\Posts\Post;
use Torpedo\Wp\ViewElements\CardList;

interface isCard
{
	static function createFromPost(CardListable $post);
}

