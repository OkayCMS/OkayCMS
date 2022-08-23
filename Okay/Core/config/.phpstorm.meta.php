<?php namespace PHPSTORM_META {
    override(\Okay\Core\OkayContainer\OkayContainer::get(), map(['' => '@']));
    override(\Okay\Core\ServiceLocator::getService(), map(['' => '@']));
    override(\Okay\Core\EntityFactory::get(), map(['' => '@']));
    override(sql_injection_subst(), map(['__' => 'ok_']));
}