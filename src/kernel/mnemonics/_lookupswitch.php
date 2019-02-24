<?php
namespace PHPJava\Kernel\Mnemonics;

use PHPJava\Exceptions\NotImplementedException;
use PHPJava\Utilities\BinaryTool;

final class _lookupswitch implements OperationInterface
{
    use \PHPJava\Kernel\Core\Accumulator;
    use \PHPJava\Kernel\Core\ConstantPool;

    public function execute(): void
    {
        $key = $this->getStack();

        $paddingData = $this->readByte() + $this->readByte() + $this->readByte();

        $offsets = array();

        $offsets['default'] = $this->readInt();
        $switchSize = $this->readUnsignedInt();


        for ($i = 0; $i < $switchSize; $i++) {
            $label = $this->readInt();

            $offsets[(string) $label] = $this->readInt();
        }

        if (isset($offsets[$key])) {

            // goto PC
            $this->setOffset($this->getPointer() + $offsets[$key]);
            return;
        }

        // goto default
        $this->setOffset($this->getPointer() + $offsets['default']);
    }
}