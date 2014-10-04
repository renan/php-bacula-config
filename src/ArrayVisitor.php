<?php
namespace Renan\Bacula\Config;

use Hoa\Visitor;
use UnexpectedValueException;

class ArrayVisitor implements Visitor\Visit
{
    public function visit(Visitor\Element $element, &$handle = null, $eldnah = null)
    {
        $id = $element->getId();
        switch ($id) {
            case 'token':
                $value = $element->getValue();
                return $value['value'];

            case '#root':
                return $this->root($element, $handle, $eldnah);

            case '#block':
                return $this->block($element, $handle, $eldnah);

            case '#pair':
                return $this->pair($element, $handle, $eldnah);
        }
        throw new UnexpectedValueException(sprintf('I cannot visit %s yet.', $id));
    }

    protected function root(Visitor\Element $element, &$handle, $eldnah)
    {
        $children = $element->getChildren();
        $out = [];
        foreach ($children as $child) {
            $out[] = $child->accept($this, $handle, $eldnah);
        }
        return $out;
    }

    protected function block(Visitor\Element $element, &$handle, $eldnah)
    {
        $children = $element->getChildren();
        $out = [];
        foreach ($children as $i => $child) {
            if ($i === 0) {
                $name = $child->accept($this, $handle, $eldnah);
            } else {
                list($key, $value) = $child->accept($this, $handle, $eldnah);
                $out[$key][] = $value;
            }
        }
        return [$name, $out];
    }

    protected function pair(Visitor\Element $element, &$handle, $eldnah)
    {
        $children = $element->getChildren();
        return [
            $children[0]->accept($this, $handle, $eldnah),
            $children[1]->accept($this, $handle, $eldnah),
        ];
    }
}
