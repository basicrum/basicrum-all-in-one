<?php

declare(strict_types=1);

namespace App\BasicRum;

class DiagramBaseJson
{
    /**
     * DiagramBaseJson constructor.
     *
     * @throws \Exception
     */
    public function __construct($type)
    {
        //$this->type = $type;

        switch ($type) {
            case 'distribution':
                // code...
                break;

            case 'time_series':
                break;

            case 'plane':
                break;

            default:
                // code...
                break;
        }
    }

    public function distributionTemplate()
    {
        $json = "
            {
                title: 'Diagram Title',
                global: {
                    presentation: {
                        'render_type': 'distribution'
                    },
                    data_requirements: {
                        period: {
                            type: 'moving',
                            start: '30',
                            end: 'now',
                        }
                    }
                },
                segments: {
                    1: {
                        presentation: {
                            name: 'Segment #1 Name',
                            color: '#1F77B4'
                        },
                        data_requirements: {
                            filters: {
                                device_type: {
                                    search_value: 2,
                                    condition: 'is'
                                }
                            },
                            business_metrics: {
                                page_views_count: {
                                    data_flavor: {
                                        count: true
                                    }
                                }
                            }
                        }
                    },
                    2: {
                        presentation: {
                            name: 'Tablet',
                            color: '#ff6023'
                        },
                        data_requirements: {
                            filters: {
                                device_type: {
                                    search_value: 3,
                                    condition: 'is'
                                }
                            },
                            business_metrics: {
                                page_views_count: {
                                    data_flavor: {
                                        count: true
                                    }
                                }
                            }
                        }
                    },
                    3: {
                        presentation: {
                            name: 'Mobile',
                            color: '#2CA02C',
                        },
                        data_requirements: {
                            filters: {
                                device_type: {
                                    search_value: 1,
                                    condition: 'is'
                                }
                            },
                            business_metrics: {
                                page_views_count: {
                                    data_flavor: {
                                        count: true
                                    }
                                }
                            }
                        }
                    },
                    4: {
                        presentation: {
                            name: 'Bot',
                            color: '#000000'
                        },
                        data_requirements: {
                            filters: {
                                device_type: {
                                    search_value: 4,
                                    condition: 'is'
                                }
                            },
                            business_metrics: {
                                page_views_count: {
                                    data_flavor: {
                                        count: true
                                    }
                                }
                            }
                        }
                    }
                }
            }
        ";
    }
}
