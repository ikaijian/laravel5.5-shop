<?php

namespace App\Admin\Controllers;

use App\Models\Product;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class ProductsController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('商品列表')
            ->description('所有商品')
            ->body($this->grid());
    }

//    /**
//     * Show interface.
//     *
//     * @param mixed   $id
//     * @param Content $content
//     * @return Content
//     */
//    public function show($id, Content $content)
//    {
//        return $content
//            ->header('Detail')
//            ->description('description')
//            ->body($this->detail($id));
//    }
//
    /**
     * Edit interface.
     *
     * @param mixed   $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('编辑商品')
//            ->description('description')
            ->body($this->form()->edit($id));
    }

    /**更新商品
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function update($id)
    {
        return $this->form()->update($id);
    }

    /**
     * 新增商品.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('创建商品')
//            ->description('description')
            ->body($this->form());
    }

    /**保存信息
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store()
    {
        return $this->form()->store();
    }
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Product);

        $grid->id('ID')->sortable();
        $grid->title('商品名称');
        $grid->on_sale('已上架')->display(function ($value) {
            return $value ? '是' : '否';
        });
        $grid->sold_count('销量');
        $grid->review_count('评论数');
        $grid->price('价格');
        $grid->rating('评分');
        $grid->created_at('创建时间');

        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableDelete();
        });
        $grid->tools(function ($tools) {
            // 禁用批量删除按钮
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });

        return $grid;
    }

//    /**
//     * Make a show builder.
//     *
//     * @param mixed   $id
//     * @return Show
//     */
//    protected function detail($id)
//    {
//        $show = new Show(Product::findOrFail($id));
//
//        $show->id('Id');
//        $show->title('Title');
//        $show->description('Description');
//        $show->image('Image');
//        $show->on_sale('On sale');
//        $show->rating('Rating');
//        $show->sold_count('Sold count');
//        $show->review_count('Review count');
//        $show->price('Price');
//        $show->created_at('Created at');
//        $show->updated_at('Updated at');
//
//        return $show;
//    }
//

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        // 创建一个表单
        $form = new Form(new Product);
        // 创建一个输入框，第一个参数 title 是模型的字段名，第二个参数是该字段描述
        $form->text('title', '商品名称')->rules('required');

        // 创建一个选择图片的框
        $form->image('image', '封面图片')->rules('required|image');

        // 创建一个富文本编辑器
        $form->editor('description', '商品描述')->rules('required');

        // 创建一组单选框
        $form->radio('on_sale', '上架')->options(['1' => '是', '0'=> '否'])->default('0');


        // 直接添加一对多的关联模型
        //可以在表单中直接添加一对多的关联模型，商品和商品 SKU 的关系就是一对多，第一个参数必须和主模型中定义此关联关系的方法同名，
        //之前在 App\Models\Product 类中定义了 skus() 方法来关联 SKU，因此这里需要填入 skus，
        //第二个参数是对这个关联关系的描述，第三个参数是一个匿名函数，用来定义关联模型的字段。
        $form->hasMany('skus', 'SKU 列表', function (Form\NestedForm $form) {
            $form->text('title', 'SKU 名称')->rules('required');
            $form->text('description', 'SKU 描述')->rules('required');
            $form->text('price', '单价')->rules('required|numeric|min:0.01');
            $form->text('stock', '剩余库存')->rules('required|integer|min:0');
        });

        // 定义事件回调，当模型即将保存时会触发这个回调
        //需要在保存商品之前拿到所有 SKU 中最低的价格作为商品的价格，然后通过 $form->model()->price 存入到商品模型中
        $form->saving(function (Form $form) {
            //collect() 函数是 Laravel 提供的一个辅助函数，可以快速创建一个 Collection 对象
            //把用户提交上来的 SKU 数据放到 Collection 中，利用 Collection 提供的 min() 方法求出所有 SKU 中最小的 price，
            //后面的 ?: 0 则是保证当 SKU 数据为空时 price 字段被赋值 0  Form::REMOVE_FLAG_NAME
            $form->model()->price = collect($form->input('skus'))->where(Form::REMOVE_FLAG_NAME, 0)->min('price') ?: 0;

        });

        return $form;
    }
}
