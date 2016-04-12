<?php
namespace MonarcCore\Service;

class IndexServiceFactory extends AbstractServiceFactory
{
	protected $ressources = array(
		'recipeTable'=> '\MonarcCore\Model\Table\RecipeTable',
		'recipeEntity'=> '\MonarcCore\Model\Entity\Recipe',
	);
}
