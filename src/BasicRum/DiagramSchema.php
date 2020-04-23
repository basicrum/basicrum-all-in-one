<?php

declare(strict_types=1);

namespace App\BasicRum;

use App\BasicRum\Filters\Collaborator as Filters;

class DiagramSchema
{
    private $definitionSegment;
    private $layout;
    private $filters;
    private $properties;
    private $schema;

    /**
     * SchemaOrchestrator constructor.
     *
     * @throws \Exception
     */
    public function __construct($type)
    {
        $this->type = $type;
        $this->generateLayout();

        $this->generateDefinitionSegment();
        // $this->generateFilters();
        // $this->generateProprties();
    }

    public function generateDefinitionSegment()
    {
        $segment = '
            "segment": {
                "type": "object",
                "properties": {
                    "presentation": {
                        "type": "object",
                        "properties": {
                            "name": {
                                "type": "string",
                                "title": "Segment Name"
                            },
                            "color": {
                                "type": "string",
                                "title": "Segment Color"
                            },
        ';

        if ('time_series' == $this->type) {
            $segment .= '
                                "type": {
                                    "enum": ["bar"],
                                }
            ';
        }

        $segment .= '
                        },
                    },
                    "data_requirements": {
                        "type": "object",
                        "properties": {
                            "technical_metrics": {
                                "type": "object",
                                "properties": {
                                    "total_img_size": {
                                        "type": "object",
                                        "properties": {
                                            "data_flavor": {
                                                "type": "object",
                                                "properties": {
                                                    "percentile": {
                                                        "title": "Some test title",
                                                        "type": "integer"
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        ';

        $this->definitionSegment = $segment;
    }

    public function generateLayout()
    {
        $this->layout = '';
        if ('time_series' == $this->type) {
            $this->layout = '"layout" : {
                                "title": "Layout",
                                "type": "object",
                                "properties": {
                                    "bargap": {
                                        "description": "Bargap",
                                        "type": "integer",
                                        "minimum": 0
                                    },
                                    "barmode": {
                                        "enum": ["overlay"]
                                    }
                                }
                            }';
        }
    }

    public function generateGlobalFilters()
    {
        $filter = new Filters();
        // $value = $filter->applyForRequirement(['browser_name' => []]);
        // $value = $filter->getAllPossibleFiltersSchema();
        // print_r($value); exit();
        $this->filters = $filter->getAllPossibleFiltersSchema();
    }

    public function generateSegmentFilters()
    {
    }

    public function generateProperties()
    {
    }

    public function generateSchema()
    {
        $this->generateGlobalFilters();

        $schema = '"$schema": "http://json-schema.org/draft-07/schema#",
        "definitions": {
            '.$this->definitionSegment.'
        },
        "type": "object",
        "properties": {
            "global": {
                "type": "object",
                "properties": {
                    "presentation": {
                        "title": "Presentation part",
                        "type": "object",
                        "properties": {
                            "render_type": {
                                "title": "Widget Type",
                                "enum": ["time_series", "distribution", "plane"]
                            },
                            '.$this->layout.'
                        }
                    },
                    "data_requirements": {
                        "type": "object",
                        "properties": {
                            "period": {
                               "type": "object",
                                "properties": {
                                    "type": {
                                        "title": "Type",
                                        "enum": ["moving"]
                                    },
                                    "start": {
                                        "title": "Start",
                                        "enum": ["30"],
                                        "type": "integer"
                                    },
                                    "end": {
                                        "Title": "End Date",
                                        "enum": ["now"]
                                    }
                                }
                            },
                            '.$this->filters.'
                        }
                    }
                }
            },
            "segments": {
                "type": "object",
                "properties": {
                    "1": {"$ref": "#/definitions/segment"}
                }
            }
        }
        ';

        return $schema;
    }
}
