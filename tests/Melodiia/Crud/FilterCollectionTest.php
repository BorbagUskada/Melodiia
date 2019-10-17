<?php

namespace Biig\Melodiia\Test\Crud\Controller;

use Biig\Melodiia\Crud\FilterCollection;
use Biig\Melodiia\Crud\FilterInterface;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class FilterCollectionTest extends TestCase
{
    /** @var FormFactoryInterface|ObjectProphecy */
    private $formFactory;

    public function setUp()
    {
        $formBuilder = $this->prophesize(FormBuilderInterface::class);
        $formBuilder->getForm()->willReturn($this->prophesize(FormInterface::class)->reveal());
        $this->formFactory = $this->prophesize(FormFactoryInterface::class);
        $this->formFactory->createNamedBuilder(Argument::cetera())->willReturn($formBuilder->reveal());
    }

    public function testItFilterQuery()
    {
        $filter = $this->prophesize(FilterInterface::class);
        $filter->filter(Argument::cetera())->shouldBeCalled();
        $filter->buildForm(Argument::cetera());
        $collection = new FilterCollection($this->formFactory->reveal(), [$filter->reveal()]);
        $collection->getForm();
        $collection->filter($this->prophesize(QueryBuilder::class)->reveal());
    }

    /**
     * @expectedException \TypeError
     */
    public function testItDoesNotAcceptSomethingElseThanFilter()
    {
        $collection = new FilterCollection($this->formFactory->reveal(), [new \stdClass()]);
    }

    /**
     * @expectedException \TypeError
     */
    public function testItDoesNotAcceptSomethingElseThanFilterInAdd()
    {
        $collection = new FilterCollection($this->formFactory->reveal());
        $collection->add(new \stdClass());
    }

    public function testItBuildAForm()
    {
        /** @var FormBuilderInterface|ObjectProphecy $builder */
        $builder = $this->prophesize(FormBuilderInterface::class);
        $builder->add('fake', TextType::class)->shouldBeCalled();
        $builder->getForm()->willReturn($form = $this->prophesize(FormInterface::class)->reveal())->shouldBeCalled();
        $this->formFactory->createNamedBuilder(Argument::cetera())->willReturn($builder->reveal());

        $collection = new FilterCollection($this->formFactory->reveal(), [new FakeFilter()]);
        $collection->getForm();
    }

    public function testItBuildsAFormEvenWithNoFilters()
    {
        /** @var FormBuilderInterface|ObjectProphecy $builder */
        $builder = $this->prophesize(FormBuilderInterface::class);
        $builder->add('fake', TextType::class)->shouldNotBeCalled();
        $builder->getForm()->willReturn($form = $this->prophesize(FormInterface::class)->reveal())->shouldBeCalled();
        $this->formFactory->createNamedBuilder(Argument::cetera())->willReturn($builder->reveal());

        $collection = new FilterCollection($this->formFactory->reveal(), []);
        $collection->getForm();
    }
}

class FakeFilter implements FilterInterface
{
    public function filter($queryBuilder, FormInterface $form): void
    {
        // this is fake
    }

    public function supports(string $class): bool
    {
        // this is fake
    }

    public function buildForm(FormBuilderInterface $formBuilder): void
    {
        $formBuilder->add('fake', TextType::class);
    }
}