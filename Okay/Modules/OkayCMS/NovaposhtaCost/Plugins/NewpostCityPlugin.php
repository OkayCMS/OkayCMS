<?php


namespace Okay\Modules\OkayCMS\NovaposhtaCost\Plugins;


use Okay\Core\Design;
use Okay\Core\EntityFactory;
use Okay\Core\SmartyPlugins\Modifier;
use Okay\Modules\OkayCMS\NovaposhtaCost\Entities\NPCitiesEntity;

class NewpostCityPlugin extends Modifier
{
    protected $tag = 'newpost_city';

    protected $design;
    
    /** @var NPCitiesEntity */
    protected $citiesEntity;

    public function __construct(Design $design, EntityFactory $entityFactory)
    {
        $this->design = $design;
        $this->citiesEntity = $entityFactory->get(NPCitiesEntity::class);
    }

    public function run($cityRef)
    {
        if (empty($cityRef)) {
            return '';
        }

        return $this->citiesEntity->cols(['name'])->findOne(['ref' => $cityRef]);
    }
}