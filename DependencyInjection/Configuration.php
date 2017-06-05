<?php
namespace Czesio\NestablePageBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('czesio_nestable_page');
        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        $rootNode
            ->children()
            ->scalarNode('page_entity')->defaultValue('Czesio\NestablePageBundle\PageTestBundle\Entity\Page')->end()
            ->scalarNode('pagemeta_entity')->defaultValue('Czesio\NestablePageBundle\PageTestBundle\Entity\PageMeta')->end()
            ->scalarNode('page_form_type')->defaultValue('Czesio\NestablePageBundle\PageTestBundle\Form\PageType')->end()
            ->scalarNode('pagemeta_form_type')->defaultValue('Czesio\NestablePageBundle\PageTestBundle\Form\PageMetaType')->end()
            ->scalarNode('page_view_list')->defaultValue('CzesioNestablePageBundle:Page:list.html.twig')->end()
            ->scalarNode('page_view_edit')->defaultValue('CzesioNestablePageBundle:Page:edit.html.twig')->end()
            ->scalarNode('page_view_show')->defaultValue('CzesioNestablePageBundle:Page:show.html.twig')->end()
            ->scalarNode('page_view_new')->defaultValue('CzesioNestablePageBundle:Page:new.html.twig')->end()
            ->scalarNode('pagemeta_view_new')->defaultValue('CzesioNestablePageBundle:PageMeta:new.html.twig')->end()
            ->scalarNode('pagemeta_view_edit')->defaultValue('CzesioNestablePageBundle:PageMeta:edit.html.twig')->end()
            ->scalarNode('pagemeta_view_index')->defaultValue('CzesioNestablePageBundle:PageMeta:index.html.twig')->end()
            ->scalarNode('pagemeta_view_show')->defaultValue('CzesioNestablePageBundle:PageMeta:show.html.twig')->end()
            ->end()
        ;
        return $treeBuilder;
    }
}