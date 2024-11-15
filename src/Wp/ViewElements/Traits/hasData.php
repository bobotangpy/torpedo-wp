<?php

namespace Torpedo\Wp\ViewElements\Traits;

trait hasData
{
    /** @var array */
    protected $data_attributes = [];

    /**
     * @return array
     */
    public function getDataAttributes()
    {
        //return $this->data;
        $out = '';
        foreach($this->data_attributes as $key => $value) {
            $out .= "data-$key=\"$value\" ";
        }
        return $out;
    }
    /**
     * @param array $data
     * @return hasData
     */
    public function setDataAttributes(array $data)
    {
        $this->data_attributes = $data;
        return $this;
    }

    public function addDataAttribute($key, $value)
    {
        $this->data_attributes[$key] = $value;
        return $this;
    }
}
