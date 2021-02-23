<?php


namespace Okay\Admin\Requests;


use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\Request;
use Okay\Core\Translit;

class BackendBlogRequest
{
    /**
     * @var Request
     */
    private $request;
    
    /**
     * @var Translit
     */
    private $translit;

    public function __construct(Request $request, Translit $translit)
    {
        $this->request = $request;
        $this->translit = $translit;
    }

    public function postCheck()
    {
        $check = $this->request->post('check');
        return ExtenderFacade::execute(__METHOD__, $check, func_get_args());
    }

    public function postAction()
    {
        $action = $this->request->post('action');
        return ExtenderFacade::execute(__METHOD__, $action, func_get_args());
    }
    
    public function postArticle()
    {
        $post = new \stdClass;
        $post->id        = $this->request->post('id', 'integer');
        $post->author_id = $this->request->post('author_id', 'integer');
        $post->read_time = $this->request->post('read_time', 'integer');
        $post->name      = $this->request->post('name');
        $post->date      = date('Y-m-d H:i:s', strtotime($this->request->post('date')));
        $post->rating = $this->request->post('rating', 'float');
        $post->votes  = $this->request->post('votes', 'integer');
        
        if (($time = strtotime($this->request->post('updated_date'))) > 0) {
            $post->updated_date = date('Y-m-d', $time);
        } else {
            $post->updated_date = null;
        }
        $post->url              = trim($this->request->post('url', 'string'));
        $post->visible          = $this->request->post('visible', 'integer');
        $post->show_table_content = $this->request->post('show_table_content', 'integer');
        $post->meta_title       = $this->request->post('meta_title');
        $post->meta_keywords    = $this->request->post('meta_keywords');
        $post->meta_description = $this->request->post('meta_description');

        $post->annotation  = $this->request->post('annotation');
        $post->description = $this->request->post('description');
        if (empty($post->url)) {
            $post->url = $this->translit->translit($post->name);
        }

        return ExtenderFacade::execute(__METHOD__, $post, func_get_args());
    }

    public function postDeleteImage()
    {
        $deleteImage = $this->request->post('delete_image');
        return ExtenderFacade::execute(__METHOD__, $deleteImage, func_get_args());
    }

    public function postCategories()
    {
        $postCategories = $this->request->post('categories');
        if (is_array($postCategories)) {
            $pc = [];
            foreach ($postCategories as $c) {
                $x = new \stdClass();
                $x->id = $c;
                $pc[$x->id] = $x;
            }
            $postCategories = $pc;
        }

        return ExtenderFacade::execute(__METHOD__, $postCategories, func_get_args());
    }
    
    public function fileImage()
    {
        $image = $this->request->files('image');
        return ExtenderFacade::execute(__METHOD__, $image, func_get_args());
    }

    public function postRelatedProducts()
    {
        if (is_array($this->request->post('related_products'))) {
            $rp = [];
            foreach($this->request->post('related_products') as $p) {
                $rp[$p] = new \stdClass();
                $rp[$p]->post_id = $this->request->post('id', 'integer');
                $rp[$p]->related_id = $p;
            }
            $relatedProducts = $rp;
        } else {
            $relatedProducts = [];
        }

        return ExtenderFacade::execute(__METHOD__, $relatedProducts, func_get_args());
    }
    
}