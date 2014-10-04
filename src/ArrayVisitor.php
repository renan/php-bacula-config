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
                return $this->visitChildren($element, $handle, $eldnah);

            case '#resource':
                $children = $this->visitChildren($element, $handle, $eldnah);
                return [
                    'type'     => strtolower(array_shift($children)),
                    'resource' => $children,
                ];

            case '#pair':
                $children = $this->visitChildren($element, $handle, $eldnah);
                return [
                    'key'   => $children[0],
                    'value' => $children[1],
                ];

            default:
                throw new UnexpectedValueException(sprintf('I cannot visit %s yet.', $id));
        }
    }

    protected function visitChildren(Visitor\Element $element, &$handle, $eldnah)
    {
        $children = $element->getChildren();
        $out = [];
        foreach ($children as $child) {
            $out[] = $child->accept($this, $handle, $eldnah);
        }
        return $out;
    }
}
