<?php
/**
 * Created by PhpStorm.
 * User: Dmytro Portenko
 * Date: 8/12/18
 * Time: 4:27 PM
 */

namespace UpworkPortenko\Wysiwyg\Helper\Wysiwyg;

class Images extends \Magento\Cms\Helper\Wysiwyg\Images
{

    protected $scopeConfig;

    /**
     * Construct
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Backend\Helper\Data $backendData
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Backend\Helper\Data $backendData,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context, $backendData, $filesystem, $storeManager);
        $this->scopeConfig = $scopeConfig;
    }

    public function getImageHtmlDeclaration($filename, $renderAsTag = false)
    {
        $fileurl = $this->getCurrentUrl() . $filename;

        $adminUrl = $this->scopeConfig->getValue('admin/url/custom');
        $baseUrl = $this->scopeConfig->getValue('web/secure/base_url');
        $fileurl = str_replace($adminUrl, $baseUrl, $fileurl);

        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $mediaPath = str_replace($mediaUrl, '', $fileurl);
        $directive = sprintf('{{media url="%s"}}', $mediaPath);
        if ($renderAsTag) {
            $html = sprintf('<img src="%s" alt="" />', $this->isUsingStaticUrlsAllowed() ? $fileurl : $directive);
        } else {
            if ($this->isUsingStaticUrlsAllowed()) {
                $html = $fileurl; // $mediaPath;
            } else {
                $directive = $this->urlEncoder->encode($directive);

                $html = $this->_backendData->getUrl(
                    'cms/wysiwyg/directive',
                    [
                        '___directive' => $directive,
                        '_escape_params' => false,
                    ]
                );
            }
        }
        return $html;
    }
}