<?php

namespace Deity\MagentoApi\Helper\Catalog;

class Image extends \Magento\Catalog\Helper\Image
{
    public function getFrame()
    {
        $frame = $this->getAttribute('frame');
        if (is_null($frame)) {
            $frame = $this->getConfigView()->getVarValue('Magento_Catalog', 'product_image_white_borders');
        }

        return (bool)$frame;
    }

    protected function setImageProperties()
    {
        $this->_getModel()->setDestinationSubdir($this->getType());

        $this->_getModel()->setWidth($this->getWidth());
        $this->_getModel()->setHeight($this->getHeight());

        // Set 'keep frame' flag
        $frame = $this->getFrame();
        $this->_getModel()->setKeepFrame($frame);

        // Set 'constrain only' flag
        $constrain = $this->getAttribute('constrain');
        if (!is_null($constrain)) {
            $this->_getModel()->setConstrainOnly((bool)$constrain);
        }

        // Set 'keep aspect ratio' flag
        $aspectRatio = $this->getAttribute('aspect_ratio');
        if (!is_null($aspectRatio)) {
            $this->_getModel()->setKeepAspectRatio((bool)$aspectRatio);
        }

        // Set 'transparency' flag
        $transparency = $this->getAttribute('transparency');
        if (!is_null($transparency)) {
            $this->_getModel()->setKeepTransparency((bool)$transparency);
        }
        // Set background color
        $background = $this->getAttribute('background');
        if (!empty($background)) {
            $this->_getModel()->setBackgroundColor($background);
        }

        return $this;
    }

}
