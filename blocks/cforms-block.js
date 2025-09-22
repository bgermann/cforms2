/**
 * cforms2 Gutenberg Block
 * Modern block editor integration for cforms2
 */

const { registerBlockType } = wp.blocks;
const { SelectControl } = wp.components;
const { useSelect } = wp.data;
const { __ } = wp.i18n;

registerBlockType('cforms2/form', {
    title: __('cforms2 Form', 'cforms2'),
    description: __('Insert a cforms2 contact form', 'cforms2'),
    category: 'widgets',
    icon: 'feedback',
    keywords: [
        __('form', 'cforms2'),
        __('contact', 'cforms2'),
        __('cforms', 'cforms2')
    ],
    attributes: {
        formId: {
            type: 'string',
            default: '1'
        },
        formName: {
            type: 'string',
            default: ''
        }
    },
    supports: {
        html: false,
        customClassName: false
    },

    edit: function(props) {
        const { attributes, setAttributes } = props;
        const { formId, formName } = attributes;

        // Get available forms from cforms2 settings
        const forms = useSelect((select) => {
            // This would need to be populated via REST API or localized script
            return window.cforms2_forms || [
                { id: '1', name: 'Contact Form' },
                { id: '2', name: 'Newsletter Signup' }
            ];
        }, []);

        const formOptions = forms.map(form => ({
            label: form.name,
            value: form.id
        }));

        return wp.element.createElement(
            'div',
            { className: 'cforms2-block-editor' },
            [
                wp.element.createElement(
                    'div',
                    { 
                        key: 'icon',
                        className: 'cforms2-block-icon',
                        style: {
                            textAlign: 'center',
                            padding: '20px',
                            border: '2px dashed #ccc',
                            borderRadius: '4px',
                            backgroundColor: '#f9f9f9'
                        }
                    },
                    [
                        wp.element.createElement(
                            'span',
                            { 
                                key: 'dashicon',
                                className: 'dashicons dashicons-feedback',
                                style: { fontSize: '48px', color: '#666' }
                            }
                        ),
                        wp.element.createElement(
                            'h3',
                            { key: 'title' },
                            __('cforms2 Form', 'cforms2')
                        )
                    ]
                ),
                wp.element.createElement(
                    SelectControl,
                    {
                        key: 'select',
                        label: __('Select Form', 'cforms2'),
                        value: formId,
                        options: [
                            { label: __('Choose a form...', 'cforms2'), value: '' },
                            ...formOptions
                        ],
                        onChange: (newFormId) => {
                            const selectedForm = forms.find(form => form.id === newFormId);
                            setAttributes({
                                formId: newFormId,
                                formName: selectedForm ? selectedForm.name : ''
                            });
                        }
                    }
                ),
                formId && wp.element.createElement(
                    'div',
                    { 
                        key: 'preview',
                        className: 'cforms2-block-preview',
                        style: {
                            marginTop: '15px',
                            padding: '15px',
                            backgroundColor: '#e8f4fd',
                            border: '1px solid #0073aa',
                            borderRadius: '4px'
                        }
                    },
                    [
                        wp.element.createElement(
                            'strong',
                            { key: 'preview-label' },
                            __('Form Preview:', 'cforms2')
                        ),
                        wp.element.createElement(
                            'p',
                            { key: 'preview-text' },
                            __('Form ID:', 'cforms2') + ' ' + formId + (formName ? ' (' + formName + ')' : '')
                        ),
                        wp.element.createElement(
                            'p',
                            { 
                                key: 'preview-note',
                                style: { fontSize: '12px', color: '#666' }
                            },
                            __('The actual form will be displayed on the frontend.', 'cforms2')
                        )
                    ]
                )
            ]
        );
    },

    save: function() {
        // Return null for server-side rendering
        return null;
    }
});
