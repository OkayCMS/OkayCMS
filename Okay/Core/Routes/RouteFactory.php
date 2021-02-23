<?php


namespace Okay\Core\Routes;


class RouteFactory
{
    public function create($routeName, $params = [])
    {
        if ($routeName === 'product') {
            return new ProductRoute($params);
        }

        if ($routeName === 'category') {
            return new CategoryRoute($params);
        }

        if ($routeName === 'brand') {
            return new BrandRoute($params);
        }

        if ($routeName === 'brands') {
            return new AllBrandsRoute($params);
        }

        if ($routeName === 'blog_category') {
            return new BlogCategoryRoute($params);
        }

        if ($routeName === 'post') {
            return new PostRoute($params);
        }

        if ($routeName === 'page') {
            return new PageRoute($params);
        }

        return false;
    }
}