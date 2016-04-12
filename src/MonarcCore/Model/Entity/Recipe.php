<?php
namespace MonarcCore\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Recipe
 *
 * @package MonarcCore\Model\Entity
 * @ORM\Table(name="Recipe")
 * @ORM\Entity
 */
class Recipe extends AbstractEntity
{
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	protected $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="label", type="string")
	 */
	protected $label;
}
