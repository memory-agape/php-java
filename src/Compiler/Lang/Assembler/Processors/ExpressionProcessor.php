<?php
declare(strict_types=1);
namespace PHPJava\Compiler\Lang\Assembler\Processors;

use PHPJava\Compiler\Builder\Signatures\Descriptor;
use PHPJava\Compiler\Lang\Assembler\Processors\Traits\AssignableFromNode;
use PHPJava\Compiler\Lang\Assembler\Processors\Traits\ConstLoadableFromNode;
use PHPJava\Compiler\Lang\Assembler\Processors\Traits\ConstractableFromNode;
use PHPJava\Compiler\Lang\Assembler\Processors\Traits\MagicConstLoadableFromNode;
use PHPJava\Compiler\Lang\Assembler\Processors\Traits\MethodCallableFromNode;
use PHPJava\Compiler\Lang\Assembler\Processors\Traits\OperationCalculatableFromNode;
use PHPJava\Compiler\Lang\Assembler\Processors\Traits\PostDecrementableFromNode;
use PHPJava\Compiler\Lang\Assembler\Processors\Traits\PostIncrementableFromNode;
use PHPJava\Compiler\Lang\Assembler\Processors\Traits\PrintableFromNode;
use PHPJava\Compiler\Lang\Assembler\Processors\Traits\StringLoadableFromNode;
use PHPJava\Compiler\Lang\Assembler\Processors\Traits\VariableLoadableFromNode;
use PHPJava\Compiler\Lang\Assembler\Traits\Calculatable;
use PHPJava\Compiler\Lang\Assembler\Traits\Enhancer\Operation\Conditionable;
use PHPJava\Compiler\Lang\Assembler\Traits\Enhancer\Operation\FieldLoadable;
use PHPJava\Compiler\Lang\Assembler\Traits\Enhancer\Operation\LocalVariableAssignable;
use PHPJava\Compiler\Lang\Assembler\Traits\Enhancer\Operation\LocalVariableLoadable;
use PHPJava\Compiler\Lang\Assembler\Traits\Enhancer\Operation\MethodCallable;
use PHPJava\Compiler\Lang\Assembler\Traits\Enhancer\Operation\NumberLoadable;
use PHPJava\Compiler\Lang\Assembler\Traits\Enhancer\Operation\Outputable;
use PHPJava\Compiler\Lang\Assembler\Traits\NodeConvertible;
use PHPJava\Compiler\Lang\Assembler\Traits\OperationManageable;
use PHPJava\Compiler\Lang\Assembler\Traits\ParameterParseable;
use PHPJava\Exceptions\AssembleStructureException;
use PHPJava\Kernel\Maps\OpCode;
use PHPJava\Kernel\Resolvers\MnemonicResolver;
use PHPJava\Kernel\Types\Int_;
use PHPJava\Packages\java\lang\Integer;
use PHPJava\Utilities\ArrayTool;
use PhpParser\Node;

class ExpressionProcessor extends AbstractProcessor implements ProcessorInterface
{
    use OperationManageable;
    use OperationCalculatableFromNode;
    use NodeConvertible;
    use FieldLoadable;
    use ConstLoadableFromNode;
    use MagicConstLoadableFromNode;
    use StringLoadableFromNode;
    use VariableLoadableFromNode;
    use AssignableFromNode;
    use PostDecrementableFromNode;
    use PostIncrementableFromNode;
    use MethodCallableFromNode;
    use PrintableFromNode;
    use NumberLoadable;
    use MethodCallable;
    use Calculatable;
    use Conditionable;
    use Outputable;
    use LocalVariableAssignable;
    use LocalVariableLoadable;
    use ParameterParseable;
    use ConstractableFromNode;

    /**
     * @param Node[] $nodes
     */
    public function execute(array $nodes, ?callable $callback = null): array
    {
        $operations = [];
        $classType = null;
        foreach ($nodes as $expression) {
            $nodeType = get_class($expression);
            switch ($nodeType) {
                case \PhpParser\Node\Expr\Assign::class:
                    ArrayTool::concat(
                        $operations,
                        ...$this->assembleAssignFromNode($expression)
                    );
                    break;
                case \PhpParser\Node\Expr\PostInc::class:
                    ArrayTool::concat(
                        $operations,
                        ...$this->assemblePostIncFromNode($expression)
                    );
                    break;
                case \PhpParser\Node\Expr\PostDec::class:
                    ArrayTool::concat(
                        $operations,
                        ...$this->assemblePostDecFromNode($expression)
                    );
                    break;
                case \PhpParser\Node\Expr\Print_::class:
                    ArrayTool::concat(
                        $operations,
                        ...$this->assemblePrintFromNode($expression)
                    );
                    break;
                case \PhpParser\Node\Scalar\String_::class:
                    ArrayTool::concat(
                        $operations,
                        ...$this->assembleLoadStringFromNode(
                            $expression,
                            $classType
                        )
                    );
                    break;
                case \PhpParser\Node\Scalar\LNumber::class:
                    ArrayTool::concat(
                        $operations,
                        ...$this->assembleLoadNumber(
                            $expression->value,
                            $classType
                        )
                    );
                    break;
                case \PhpParser\Node\Expr\Variable::class:
                    ArrayTool::concat(
                        $operations,
                        ...$this->assembleLoadVariableFromNode(
                            $expression,
                            $classType
                        )
                    );
                    break;
                case \PhpParser\Node\Expr\ConstFetch::class:
                    ArrayTool::concat(
                        $operations,
                        ...$this->assembleLoadConstFromNode(
                            $expression,
                            $classType
                        )
                    );
                    break;
                case \PhpParser\Node\Expr\New_::class:
                    ArrayTool::concat(
                        $operations,
                        ...$this->assembleConstructorFromNode(
                            $expression,
                            $classType
                        )
                    );
                    break;
                case \PhpParser\Node\Scalar\MagicConst\Class_::class: // __CLASS__
                case \PhpParser\Node\Scalar\MagicConst\Method::class: // __METHOD__
                case \PhpParser\Node\Scalar\MagicConst\Namespace_::class: // __NAMESPACE__
                case \PhpParser\Node\Scalar\MagicConst\Dir::class: // __DIR__
                case \PhpParser\Node\Scalar\MagicConst\File::class: // __FILE__
                case \PhpParser\Node\Scalar\MagicConst\Function_::class: // __FUNCTION__
                case \PhpParser\Node\Scalar\MagicConst\Trait_::class: // __TRAIT__
                case \PhpParser\Node\Scalar\MagicConst\Line::class: // __LINE__
                    ArrayTool::concat(
                        $operations,
                        ...$this
                            ->assembleLoadMagicConstFromNode(
                                $expression,
                                $classType
                            )
                    );
                    break;
                case \PhpParser\Node\Expr\BinaryOp\Concat::class:
                    ArrayTool::concat(
                        $operations,
                        ...$this->execute(
                            [
                                // Left operator.
                                $expression->left,
                                $expression->right,
                            ],
                            $callback
                        )
                    );
                    break;
                case \PhpParser\Node\Expr\BinaryOp\BooleanAnd::class:
                case \PhpParser\Node\Expr\BinaryOp\BooleanOr::class:
                case \PhpParser\Node\Expr\BinaryOp\Mul::class:
                case \PhpParser\Node\Expr\BinaryOp\Div::class:
                case \PhpParser\Node\Expr\BinaryOp\Minus::class:
                case \PhpParser\Node\Expr\BinaryOp\Plus::class:
                case \PhpParser\Node\Expr\BinaryOp\Mod::class:
                    ArrayTool::concat(
                        $operations,
                        ...$this->assembleCalculateOperationFromNode(
                            $expression->left,
                            $expression->right,
                            $this->convertNodeToOpCode($expression),
                            $callback
                        )
                    );
                    break;
                case \PhpParser\Node\Expr\BinaryOp\Greater::class:
                case \PhpParser\Node\Expr\BinaryOp\Smaller::class:
                case \PhpParser\Node\Expr\BinaryOp\GreaterOrEqual::class:
                case \PhpParser\Node\Expr\BinaryOp\SmallerOrEqual::class:
                    ArrayTool::concat(
                        $operations,
                        ...$this->assembleCalculateOperationFromNode(
                            $expression->left,
                            $expression->right,
                            OpCode::_isub,
                            $callback
                        ),
                        ...$this->assembleConditions(
                            $this->convertNodeToOpCode($expression),
                            [
                                \PHPJava\Compiler\Builder\Generator\Operation\Operation::create(
                                    OpCode::_iconst_1
                                ),
                            ],
                            [
                                \PHPJava\Compiler\Builder\Generator\Operation\Operation::create(
                                    OpCode::_iconst_0
                                ),
                            ]
                        )
                    );
                    break;
                case \PhpParser\Node\Expr\BinaryOp\NotIdentical::class:
                case \PhpParser\Node\Expr\BinaryOp\Identical::class:
                    /**
                     * @var \PhpParser\Node\Expr\BinaryOp\Identical $conditionNode
                     */
                    // Left operator.
                    $leftOperands = $this->execute(
                        [$expression->left],
                        $callback
                    );

                    // Right operator.
                    $rightOperands = $this->execute(
                        [$expression->right],
                        $callback
                    );

                    $lastLeftOperand = array_slice($leftOperands, -1, 1)[0];
                    $lastRightOperand = array_slice($rightOperands, -1, 1)[0];
                    switch ([MnemonicResolver::resolveTypeByOpCode($lastLeftOperand), MnemonicResolver::resolveTypeByOpCode($lastRightOperand)]) {
                        case [Int_::class, Int_::class]:
                            ArrayTool::concat(
                                $operations,
                                ...$leftOperands,
                                ...$rightOperands
                            );

                            ArrayTool::concat(
                                $operations,
                                ...$this->assembleStaticCallMethodOperations(
                                    Integer::class,
                                    'compare',
                                    Descriptor::factory()
                                        ->addArgument(Int_::class)
                                        ->addArgument(Int_::class)
                                        ->setReturn(Int_::class)
                                        ->make()
                                )
                            );

                            ArrayTool::concat(
                                $operations,
                                ...$this->assembleConditions(
                                    $this->convertNodeToOpCode($expression),
                                    [
                                        \PHPJava\Compiler\Builder\Generator\Operation\Operation::create(
                                            OpCode::_iconst_1
                                        ),
                                    ],
                                    [
                                        \PHPJava\Compiler\Builder\Generator\Operation\Operation::create(
                                            OpCode::_iconst_0
                                        ),
                                    ]
                                )
                            );
                            break;
                        default:
                            throw new AssembleStructureException(
                                'Unsupported operation type'
                            );
                    }
                    break;
                case \PhpParser\Node\Expr\MethodCall::class:
                    /**
                     * @var \PhpParser\Node\Expr\MethodCall $expression
                     */
                    ArrayTool::concat(
                        $operations,
                        ...$this->assembleDynamicMethodCallFromNode(
                            $expression
                        )
                    );
                    break;
                case \PhpParser\Node\Expr\StaticCall::class:
                    /**
                     * @var \PhpParser\Node\Expr\StaticCall $expression
                     */
                    ArrayTool::concat(
                        $operations,
                        ...$this->assembleStaticMethodCallFromNode(
                            $expression
                        )
                    );
                    break;
                default:
                    throw new AssembleStructureException(
                        'Unsupported expression: ' . get_class($expression)
                    );
            }

            if ($callback !== null) {
                $callback(
                    $operations,
                    $nodeType,
                    $classType
                );
            }
        }
        return $operations;
    }
}
