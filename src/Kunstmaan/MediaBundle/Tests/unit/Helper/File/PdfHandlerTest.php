<?php

namespace Kunstmaan\MediaBundle\Tests\Helper\File;

use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Helper\File\PdfHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

class PdfHandlerTest extends TestCase
{
    /** @var PdfHandler */
    protected $object;

    protected $pdfTransformer;

    /** @var string */
    protected $filesDir;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->pdfTransformer = $this->createMock('Kunstmaan\MediaBundle\Helper\Transformer\PreviewTransformerInterface');
        $mockMimeTypeGuesserfactory = $this->createMock('Kunstmaan\MediaBundle\Helper\MimeTypeGuesserFactoryInterface');
        $mockExtensionGuesserfactory = $this->createMock('Kunstmaan\MediaBundle\Helper\ExtensionGuesserFactoryInterface');
        $this->filesDir = realpath(__DIR__ . '/../../Files');

        $this->object = new PdfHandler(1, $mockMimeTypeGuesserfactory, $mockExtensionGuesserfactory);
        $this->object->setPdfTransformer($this->pdfTransformer);
    }

    public function testGetType()
    {
        $this->assertEquals(PdfHandler::TYPE, $this->object->getType());
    }

    public function testCanHandlePdfFiles()
    {
        $media = new Media();
        $media->setContent(new File($this->filesDir . '/sample.pdf'));
        $media->setContentType('application/pdf');

        $this->assertTrue($this->object->canHandle($media));
    }

    public function testCannotHandleNonPdfFiles()
    {
        $media = new Media();
        $media->setContentType('image/jpg');

        $this->assertFalse($this->object->canHandle($media));
        $this->assertFalse($this->object->canHandle(new \stdClass()));
    }

    public function testSaveMedia()
    {
        $media = new Media();
        $this->object->saveMedia($media);
    }

    public function testGetImageUrl()
    {
        $this->pdfTransformer
            ->expects($this->any())
            ->method('getPreviewFilename')
            ->will($this->returnValue('/media.pdf.jpg'));

        $media = new Media();
        $media->setUrl('/path/to/media.pdf');
        $this->assertNull($this->object->getImageUrl($media, '/basepath'));

        $previewFilename = sys_get_temp_dir() . '/media.pdf.jpg';
        $fileSystem = new Filesystem();
        $fileSystem->touch($previewFilename);
        $media->setUrl('/media.pdf');
        $this->object->setWebPath(sys_get_temp_dir());
        $this->assertEquals('/media.pdf.jpg', $this->object->getImageUrl($media, ''));
        $fileSystem->remove($previewFilename);
    }
}
