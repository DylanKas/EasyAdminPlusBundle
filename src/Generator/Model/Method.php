<?php

namespace Wandi\EasyAdminPlusBundle\Generator\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\Translator;
use Wandi\EasyAdminPlusBundle\Generator\GeneratorTool;

class Method
{
    private $name;
    private $title;
    private $actions;
    private $fields;
    private $sort;

    public function __construct()
    {
        $this->fields = new ArrayCollection();
        $this->actions = new ArrayCollection();
        $this->title = '';
        $this->sort = [
            'sort' => [],
        ];
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle($title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getActions(): ArrayCollection
    {
        return $this->actions;
    }

    public function setActions(ArrayCollection $actions): self
    {
        $this->actions = $actions;

        return $this;
    }

    public function addAction(Action $action): self
    {
        $this->actions[] = $action;

        return $this;
    }

    public function getFields(): ArrayCollection
    {
        return $this->fields;
    }

    public function setFields(ArrayCollection $fields): self
    {
        $this->fields = $fields;

        return $this;
    }

    public function addField(Field $field): self
    {
        $this->fields[] = $field;

        return $this;
    }

    public function getSort(): array
    {
        return $this->sort;
    }

    public function setSort(array $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * TODO: réécrire l'algo.
     */
    public function buildSort(array $eaToolParams): void
    {
        if (!in_array($this->name, $eaToolParams['sort']['methods'])) {
            $this->sort = [];

            return;
        }

        foreach ($eaToolParams['sort']['properties'] as $sort) {
            foreach ($this->fields as $field) {
                if ($field->getName() == $sort['name']) {
                    $this->sort['sort'] = [$sort['name'], $sort['order']];
                    break;
                }
                if (!empty($this->sort[0])) {
                    break;
                }
            }
        }
    }

    public function getStructure(array $eaToolParams): array
    {
        $actionsStructure = [];
        $fieldsStructure = [];
        $this->buildSort($eaToolParams);

        foreach ($this->actions as $action) {
            $actionsStructure[] = $action->getStructure();
        }

        foreach ($this->fields as $field) {
            if (null === $field->getName()) {
                continue;
            }
            $fieldsStructure[] = $field->getStructure();
        }

        $structure = [
            $this->name => array_merge([
                'title' => $this->title,
                'actions' => $actionsStructure,
                'fields' => $fieldsStructure,
            ], array_filter($this->sort)),
        ];

        return $structure;
    }
}
