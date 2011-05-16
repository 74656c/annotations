<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Doctrine\Common\Annotations;

use Doctrine\Common\Annotations\Cache\CacheInterface;

/**
 * A cache aware annotation reader.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class CachedReader implements ReaderInterface
{
    private $delegate;
    private $cache;

    public function __construct(ReaderInterface $reader, CacheInterface $cache)
    {
        $this->delegate = $reader;
        $this->cache = $cache;
    }

    public function getClassAnnotations(\ReflectionClass $class)
    {
        if (null !== $annots = $this->cache->getClassAnnotationsFromCache($class)) {
            return $annots;
        }

        $annots = $this->delegate->getClassAnnotations($class);
        $this->cache->putClassAnnotationsInCache($class, $annots);

        return $annots;
    }

    public function getClassAnnotation(\ReflectionClass $class, $annotationName)
    {
        foreach ($this->getClassAnnotations($class) as $annot) {
            if ($annot instanceof $annotationName) {
                return $annot;
            }
        }

        return null;
    }

    public function getPropertyAnnotations(\ReflectionProperty $property)
    {
        if (null !== $annots = $this->cache->getPropertyAnnotationsFromCache($property)) {
            return $annots;
        }

        $annots = $this->delegate->getPropertyAnnotations($property);
        $this->cache->putPropertyAnnotationsInCache($property, $annots);

        return $annots;
    }

    public function getPropertyAnnotation(\ReflectionProperty $property, $annotationName)
    {
        foreach ($this->getPropertyAnnotations($property) as $annot) {
            if ($annot instanceof $annotationName) {
                return $annot;
            }
        }

        return null;
    }

    public function getMethodAnnotations(\ReflectionMethod $method)
    {
        if (null !== $annots = $this->cache->getMethodAnnotationsFromCache($method)) {
            return $annots;
        }

        $annots = $this->delegate->getMethodAnnotations($method);
        $this->cache->putMethodAnnotationsInCache($method, $annots);

        return $annots;
    }

    public function getMethodAnnotation(\ReflectionMethod $method, $annotationName)
    {
        foreach ($this->getMethodAnnotations($method) as $annot) {
            if ($annot instanceof $annotationName) {
                return $annot;
            }
        }

        return null;
    }
}