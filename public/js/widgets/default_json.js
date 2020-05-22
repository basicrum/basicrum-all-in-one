var defaultJson = {
    title: '',
    global: {
        presentation: {
            'render_type': 'plane',
        },
        data_requirements: {
            period: {
                type: 'moving',
                start: 130,
                end: 'now',
            },
        }
    },
    segments: [
        {
            presentation: {
                name: '',
                color: '#ff6023',
                type: 'bar'
            },
            data_requirements: {
                filters: {
                    device_type: {
                        search_value: 1,
                        condition: 'is'
                    }
                },
                technical_metrics: {
                    first_paint: {
                        data_flavor: {
                            histogram: {
                                bucket: '200'
                            }
                        }
                    }
                }
            }
        }
    ]
};