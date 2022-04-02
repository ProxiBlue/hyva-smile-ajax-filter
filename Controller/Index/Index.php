<?php declare(strict_types=1);

namespace ProxiBlue\AjaxLayer\Controller\Index;

use Magento\Catalog\Model\Product\ProductList\Toolbar;
use Magento\Catalog\Model\Product\ProductList\Toolbar as ToolbarModel;
use Magento\Catalog\Model\Session as CatalogSession;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Registry;
use Hyva\Theme\ViewModel\CurrentCategory;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\Layer\Filter\DataProvider\CategoryFactory;
use Magento\Framework\App\Http\Context;

class Index implements HttpGetActionInterface
{
    /**
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var Registry|null
     */
    protected $coreRegistry = null;

    /**
     * @var CurrentCategory
     */
    protected $currentCategory;

    /**
     * @var \Magento\Catalog\Model\Layer
     */
    protected $catalogLayer;

    /**
     * @var \Magento\Catalog\Model\Layer\Filter\DataProvider\Category
     */
    private $dataProvider;

    /**
     * @var CatalogSession
     */
    private $catalogSession;

    /**
     * @param PageFactory $pageFactory
     * @param RequestInterface $request
     * @param JsonFactory $resultJsonFactory
     * @param Registry $registry
     * @param CurrentCategory $currentCategory
     * @param Resolver $layerResolver
     * @param CategoryFactory $dataProviderFactory
     * @param CatalogSession $catalogSession
     */
    public function __construct(
        PageFactory      $pageFactory,
        RequestInterface $request,
        JsonFactory      $resultJsonFactory,
        Registry         $registry,
        CurrentCategory  $currentCategory,
        Resolver         $layerResolver,
        CategoryFactory  $dataProviderFactory,
        CatalogSession   $catalogSession
    )
    {
        $this->pageFactory = $pageFactory;
        $this->request = $request;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->coreRegistry = $registry;
        $this->currentCategory = $currentCategory;
        $this->catalogLayer = $layerResolver->get();
        $this->dataProvider = $dataProviderFactory->create(['layer' => $this->catalogLayer]);
        $this->catalogSession = $catalogSession;
    }

    /**
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $categoryId = $this->request->getParam('category_id');
        $this->catalogLayer->setCurrentCategory((int)$categoryId);
        $category = $this->dataProvider->getCategory();
        $this->currentCategory->set($category);
        $this->coreRegistry->register('current_category', $category);
        $requestVars = $this->request->getParam('request_var');
        $filters = explode('&', $requestVars);
        array_pop($filters); // last one is always blank
        foreach ($filters as $filter) {
            $filterParts = explode('=', $filter);
            $this->request->setParam($filterParts[0], $filterParts[1]);
        }
        $resultPage = $this->pageFactory->create();
        $this->collection = $this->catalogLayer->getProductCollection()->addCategoryFilter($category);
        $this->collection->setCurPage(1);
        if ($limit = $this->request->getParam('page_size')) {
            $this->collection->setPageSize($limit);
            $this->catalogSession->setData(Toolbar::LIMIT_PARAM_NAME, $limit);
        }
        $blockInstance = $resultPage->getLayout()->getBlock('category.products');
        $html = $blockInstance->toHtml();
        $result = $this->resultJsonFactory->create();
        return $result->setData(['success' => true, 'category_products' => $html]);
    }
}
