<?php
namespace MonarcCore\Service;

class IndexService extends AbstractService
{
    protected $recipeTable;
    protected $recipeEntity;

    public function getList()
    {
        return $this->get('recipeTable')->fetchAll();
    }
 
    public function getEntity($id)
    {
        return $this->get('recipeTable')->get($id);
    }
}