<?php
/**
 * This file is part of PHP Mess Detector.
 *
 * Copyright (c) 2008-2012, Manuel Pichler <mapi@phpmd.org>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Manuel Pichler nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2014 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PHPMD\Rule\CleanCode;

use PDepend\Source\AST\ASTValue;
use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Rule\FunctionAware;
use PHPMD\Rule\MethodAware;

/**
 * Check for a boolean flag in the method/function signature.
 *
 * Boolean flags are signs for single responsibility principle violations.
 *
 * @author    Benjamin Eberlei <benjamin@qafoo.com>
 * @copyright 2008-2014 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class BooleanArgumentFlag extends AbstractRule implements MethodAware, FunctionAware
{
    /**
     * This method checks if a method/function has boolean flag arguments and warns about them.
     *
     * @param \PHPMD\AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node)
    {
        foreach ($node->findChildrenOfType('FormalParameter') as $param) {
            $declarator = $param->getFirstChildOfType('VariableDeclarator');
            $value = $declarator->getValue();

            if (false === $this->isBooleanValue($value)) {
                continue;
            }

            if ($this->isInheritedSignature($node)) {
                continue;
            }
            
            $this->addViolation($param, array($node->getImage(), $declarator->getImage()));
        }
    }

    private function isBooleanValue(ASTValue $value = null)
    {
        return $value && $value->isValueAvailable() && ($value->getValue() === true || $value->getValue() === false);
    }
    
    /**
     * Returns <b>true</b> when the given node is method with signature declared as inherited using
     * {@inheritdoc} annotation.
     *
     * @param PHP_PMD_AbstractNode $node The context method or function instance.
     *
     * @return boolean
     */
     private function isInheritedSignature(PHP_PMD_AbstractNode $node)
     {
        if ($node instanceof PHP_PMD_Node_Method) {
            return preg_match('/\@inheritdoc/', $node->getDocComment());
        }

        return false;
    }
}
