<?php

return [

    /*
    |--------------------------------------------------------------------------
    | 1. SECTION 8 COMPANY REGISTRATION
    |--------------------------------------------------------------------------
    */
    'section-8-company'                    => [

        'label'     => 'Section 8 Company Registration',

        'sections'  => [

            'company_details'  => [
                'label'  => 'Proposed Company Details',
                'fields' => [

                    ['name' => 'proposed_company_name', 'label' => 'Proposed Company Name (Up to 6 options)', 'type' => 'text', 'required' => true, 'validation' => 'required|string|max:255'],

                    ['name' => 'company_object', 'label' => 'Main Object / Purpose of the Company', 'type' => 'textarea', 'required' => true, 'validation' => 'required|string'],

                    ['name' => 'authorized_capital', 'label' => 'Proposed Authorised Share Capital', 'type' => 'number', 'required' => true, 'validation' => 'required|numeric|min:0'],

                    ['name' => 'paidup_capital', 'label' => 'Proposed Paid-up Share Capital', 'type' => 'number', 'required' => false, 'validation' => 'nullable|numeric|min:0'],

                    ['name' => 'registered_office_address', 'label' => 'Registered Office Address', 'type' => 'textarea', 'required' => true, 'validation' => 'required|string'],

                    ['name' => 'state', 'label' => 'State', 'type' => 'text', 'required' => true, 'validation' => 'required|string'],

                    ['name' => 'district', 'label' => 'District', 'type' => 'text', 'required' => true, 'validation' => 'required|string'],

                ],
            ],

            'director_details' => [
                'label'  => 'Director Details',
                'fields' => [

                    ['name' => 'number_of_directors', 'label' => 'Number of Directors (Minimum 2)', 'type' => 'number', 'required' => true, 'validation' => 'required|integer|min:2'],

                    [
                        'name'       => 'resident_director',
                        'label'      => 'Is at least one Director Resident in India?',
                        'type'       => 'select',
                        'required'   => true,
                        'validation' => 'required|string|in:yes,no',
                        'options'    => [
                            'yes' => 'Yes',
                            'no'  => 'No',
                        ],
                    ],

                ],
            ],

            'contact_details'  => [
                'label'  => 'Contact Details',
                'fields' => [

                    ['name' => 'contact_mobile', 'label' => 'Contact Mobile Number', 'type' => 'text', 'required' => true, 'validation' => 'required|digits:10'],

                    ['name' => 'contact_email', 'label' => 'Contact Email Address', 'type' => 'email', 'required' => true, 'validation' => 'required|email'],

                ],
            ],

        ],

        'documents' => [

            ['name' => 'director_pan', 'label' => 'Director PAN Card (Self Attested)', 'required' => true, 'mimes' => ['pdf', 'jpg', 'png']],

            ['name' => 'director_address_proof', 'label' => 'Director Address Proof (Self Attested)', 'required' => true, 'mimes' => ['pdf', 'jpg', 'png']],

            ['name' => 'passport_photo', 'label' => 'Passport Size Photo of Directors', 'required' => true, 'mimes' => ['jpg', 'png']],

            ['name' => 'office_address_proof', 'label' => 'Registered Office Address Proof (Utility Bill / Rent Agreement)', 'required' => true, 'mimes' => ['pdf', 'jpg', 'png']],

            ['name' => 'noc_from_owner', 'label' => 'NOC from Property Owner (If Rented)', 'required' => false, 'mimes' => ['pdf', 'jpg', 'png']],

            ['name' => 'moa_draft', 'label' => 'Draft Memorandum of Association (MOA)', 'required' => true, 'mimes' => ['pdf']],

            ['name' => 'aoa_draft', 'label' => 'Draft Articles of Association (AOA)', 'required' => true, 'mimes' => ['pdf']],

        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | 2. FPO REGISTRATION
    |--------------------------------------------------------------------------
    */
    'fpo-registration'                     => [

        'label'     => 'FPO Registration',

        'sections'  => [

            'basic_details'   => [
                'label'  => 'Basic FPO Details',
                'fields' => [

                    [
                        'name'       => 'fpo_type',
                        'label'      => 'Type of FPO',
                        'type'       => 'select',
                        'required'   => true,
                        'validation' => 'required|string|in:producer_company,cooperative_society,farmer_group,other',
                        'options'    => [
                            'producer_company'    => 'Producer Company',
                            'cooperative_society' => 'Cooperative Society',
                            'farmer_group'        => 'Farmer Group',
                            'other'               => 'Other',
                        ],
                    ],

                    ['name' => 'proposed_name', 'label' => 'Proposed FPO Name', 'type' => 'text', 'required' => true, 'validation' => 'required|string|max:255'],

                    ['name' => 'state', 'label' => 'State', 'type' => 'text', 'required' => true, 'validation' => 'required|string'],

                    ['name' => 'district', 'label' => 'District', 'type' => 'text', 'required' => true, 'validation' => 'required|string'],

                    ['name' => 'primary_activity', 'label' => 'Primary Agricultural / Allied Activity', 'type' => 'textarea', 'required' => true, 'validation' => 'required|string'],

                ],
            ],

            'member_details'  => [
                'label'  => 'Member Details',
                'fields' => [

                    ['name' => 'number_of_members', 'label' => 'Number of Members', 'type' => 'number', 'required' => true, 'validation' => 'required|integer|min:1'],

                    ['name' => 'average_land_holding', 'label' => 'Average Land Holding (if applicable)', 'type' => 'text', 'required' => false, 'validation' => 'nullable|string'],

                    [
                        'name'       => 'member_category',
                        'label'      => 'Member Category',
                        'type'       => 'select',
                        'required'   => false,
                        'validation' => 'nullable|string|in:small,marginal,mixed',
                        'options'    => [
                            'small'    => 'Small',
                            'marginal' => 'Marginal',
                            'mixed'    => 'Mixed',
                        ],
                    ],

                ],
            ],

            'capital_details' => [
                'label'  => 'Capital Details (Applicable for Producer Company)',
                'fields' => [

                    ['name' => 'authorized_capital', 'label' => 'Authorized Capital', 'type' => 'number', 'required' => false, 'validation' => 'nullable|numeric|min:0'],

                    ['name' => 'paidup_capital', 'label' => 'Paid-up Capital', 'type' => 'number', 'required' => false, 'validation' => 'nullable|numeric|min:0'],

                ],
            ],

            'contact_details' => [
                'label'  => 'Contact Details',
                'fields' => [

                    ['name' => 'contact_person', 'label' => 'Contact Person Name', 'type' => 'text', 'required' => true, 'validation' => 'required|string|max:255'],

                    ['name' => 'mobile', 'label' => 'Mobile Number', 'type' => 'text', 'required' => true, 'validation' => 'required|digits:10'],

                    ['name' => 'email', 'label' => 'Email Address', 'type' => 'email', 'required' => true, 'validation' => 'required|email'],

                ],
            ],

        ],

        'documents' => [

            ['name' => 'member_list', 'label' => 'List of Members (Mandatory for Registration)', 'required' => true, 'mimes' => ['pdf', 'xls', 'xlsx']],

            ['name' => 'identity_proof_members', 'label' => 'Identity Proof of Members/Promoters', 'required' => true, 'mimes' => ['pdf', 'jpg', 'png']],

            ['name' => 'land_proof', 'label' => 'Land Ownership Proof (If Applicable)', 'required' => false, 'mimes' => ['pdf', 'jpg', 'png']],

            ['name' => 'resolution_copy', 'label' => 'Resolution Copy / Consent Letter (If Applicable)', 'required' => false, 'mimes' => ['pdf']],

        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | 3. NGO / TRUST REGISTRATION
    |--------------------------------------------------------------------------
    */
    'ngo-trust-registration'               => [

        'label'     => 'NGO / Trust / Section 8 Registration',

        'sections'  => [



            'basic_details'          => [
                'label'  => 'Basic Organisation Details',
                'fields' => [
                    [
                        'name'       => 'registration_type',
                        'label'      => 'Select Registration Type',
                        'type'       => 'select',
                        'required'   => true,
                        'validation' => 'required|string|in:trust,society,section8',
                        'options'    => [
                            'trust'    => 'Trust',
                            'society'  => 'Society',
                            'section8' => 'Section 8 Company',
                        ],
                    ],

                    [
                        'name'       => 'proposed_name',
                        'label'      => 'Proposed Name',
                        'type'       => 'text',
                        'required'   => true,
                        'validation' => 'required|string|max:255'
                    ],

                    [
                        'name'       => 'registered_address',
                        'label'      => 'Registered Office Address',
                        'type'       => 'textarea',
                        'required'   => true,
                        'validation' => 'required|string'
                    ],

                    [
                        'name'       => 'state',
                        'label'      => 'State',
                        'type'       => 'text',
                        'required'   => true,
                        'validation' => 'required|string'
                    ],

                    [
                        'name'       => 'district',
                        'label'      => 'District',
                        'type'       => 'text',
                        'required'   => true,
                        'validation' => 'required|string'
                    ],

                    [
                        'name'       => 'main_objectives',
                        'label'      => 'Main Objectives / Activities',
                        'type'       => 'textarea',
                        'required'   => true,
                        'validation' => 'required|string'
                    ],

                ],
            ],

            'governing_body_details' => [
                'label'  => 'Governing Body / Trustees / Directors',
                'fields' => [

                    [
                        'name'       => 'number_of_members',
                        'label'      => 'Number of Trustees / Members / Directors',
                        'type'       => 'number',
                        'required'   => true,
                        'validation' => 'required|integer|min:2'
                    ],

                    [
                        'name'       => 'settlor_name',
                        'label'      => 'Settlor Name (For Trust Only)',
                        'type'       => 'text',
                        'required'   => false,
                        'validation' => 'nullable|string|max:255'
                    ],

                    [
                        'name'       => 'resident_director',
                        'label'      => 'At least one Director Resident in India? (For Section 8)',
                        'type'       => 'select',
                        'required'   => false,
                        'validation' => 'nullable|string|in:yes,no',
                        'options'    => [
                            'yes' => 'Yes',
                            'no'  => 'No',
                        ],
                    ],

                ],
            ],

            'capital_details'        => [
                'label'  => 'Capital Details (Applicable for Section 8 Company)',
                'fields' => [

                    [
                        'name'       => 'authorized_capital',
                        'label'      => 'Authorized Capital',
                        'type'       => 'number',
                        'required'   => false,
                        'validation' => 'nullable|numeric|min:0'
                    ],

                    [
                        'name'       => 'paidup_capital',
                        'label'      => 'Paid-up Capital',
                        'type'       => 'number',
                        'required'   => false,
                        'validation' => 'nullable|numeric|min:0'
                    ],

                ],
            ],

            'contact_details'        => [
                'label'  => 'Contact Details',
                'fields' => [

                    [
                        'name'       => 'contact_person',
                        'label'      => 'Contact Person Name',
                        'type'       => 'text',
                        'required'   => true,
                        'validation' => 'required|string|max:255'
                    ],

                    [
                        'name'       => 'mobile',
                        'label'      => 'Mobile Number',
                        'type'       => 'text',
                        'required'   => true,
                        'validation' => 'required|digits:10'
                    ],

                    [
                        'name'       => 'email',
                        'label'      => 'Email Address',
                        'type'       => 'email',
                        'required'   => true,
                        'validation' => 'required|email'
                    ],

                ],
            ],

        ],

        'documents' => [

            [
                'name'     => 'id_proof_members',
                'label'    => 'ID Proof of Trustees / Members / Directors',
                'required' => true,
                'mimes'    => ['pdf', 'jpg', 'png']
            ],

            [
                'name'     => 'address_proof_members',
                'label'    => 'Address Proof of Trustees / Members / Directors',
                'required' => true,
                'mimes'    => ['pdf', 'jpg', 'png']
            ],

            [
                'name'     => 'registered_address_proof',
                'label'    => 'Registered Office Address Proof',
                'required' => true,
                'mimes'    => ['pdf', 'jpg', 'png']
            ],

            [
                'name'     => 'trust_deed_or_moa',
                'label'    => 'Draft Trust Deed / MOA / AOA (As Applicable)',
                'required' => true,
                'mimes'    => ['pdf']
            ],

        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | 4. ITR FILING
    |--------------------------------------------------------------------------
    */
    'itr-filing'                           => [

        'label'     => 'ITR Filing (Individual / Business)',

        'sections'  => [

            'personal_details' => [
                'label'  => 'Personal Details',
                'fields' => [

                    ['name' => 'applicant_name', 'label' => 'Applicant Name (As per PAN)', 'type' => 'text', 'required' => true, 'validation' => 'required|string|max:255'],

                    ['name' => 'father_name', 'label' => 'Father Name', 'type' => 'text', 'required' => true, 'validation' => 'required|string|max:255'],

                    ['name' => 'pan_number', 'label' => 'PAN Number', 'type' => 'text', 'required' => true, 'validation' => 'required|string|size:10'],

                    ['name' => 'aadhaar_number', 'label' => 'Aadhaar Number', 'type' => 'text', 'required' => true, 'validation' => 'required|digits:12'],

                    ['name' => 'date_of_birth', 'label' => 'Date of Birth', 'type' => 'date', 'required' => true, 'validation' => 'required|date'],

                    ['name' => 'mobile', 'label' => 'Mobile Number', 'type' => 'text', 'required' => true, 'validation' => 'required|digits:10'],

                    ['name' => 'email', 'label' => 'Email Address', 'type' => 'email', 'required' => true, 'validation' => 'required|email'],

                    ['name' => 'address', 'label' => 'Residential Address', 'type' => 'textarea', 'required' => true, 'validation' => 'required|string'],

                    ['name' => 'pin_code', 'label' => 'PIN Code', 'type' => 'text', 'required' => true, 'validation' => 'required|digits:6'],

                    ['name' => 'state', 'label' => 'State', 'type' => 'text', 'required' => true, 'validation' => 'required|string'],

                ],
            ],

            'filing_details'   => [
                'label'  => 'Filing Details',
                'fields' => [

                    ['name' => 'assessment_year', 'label' => 'Assessment Year', 'type' => 'text', 'required' => true, 'validation' => 'required|string'],

                    [
                        'name'       => 'income_type',
                        'label'      => 'Income Type',
                        'type'       => 'select',
                        'required'   => true,
                        'validation' => 'required|string|in:salaried,self_employed,business,capital_gains,other',
                        'options'    => [
                            'salaried'      => 'Salaried',
                            'self_employed' => 'Self Employed',
                            'business'      => 'Business',
                            'capital_gains' => 'Capital Gains',
                            'other'         => 'Other',
                        ],
                    ],

                    ['name' => 'business_turnover', 'label' => 'Business Turnover (If Applicable)', 'type' => 'number', 'required' => false, 'validation' => 'nullable|numeric|min:0'],

                    ['name' => 'salary_income', 'label' => 'Salary Income', 'type' => 'number', 'required' => false, 'validation' => 'nullable|numeric|min:0'],

                    ['name' => 'business_income', 'label' => 'Business / Professional Income', 'type' => 'number', 'required' => false, 'validation' => 'nullable|numeric|min:0'],

                    ['name' => 'other_income', 'label' => 'Other Income (Interest / Rental / etc.)', 'type' => 'number', 'required' => false, 'validation' => 'nullable|numeric|min:0'],

                ],
            ],

            'bank_details'     => [
                'label'  => 'Bank Details',
                'fields' => [

                    ['name' => 'bank_account_number', 'label' => 'Bank Account Number', 'type' => 'text', 'required' => true, 'validation' => 'required|string'],

                    ['name' => 'ifsc_code', 'label' => 'IFSC Code', 'type' => 'text', 'required' => true, 'validation' => 'required|string'],

                ],
            ],



        ],

        'documents' => [

            ['name' => 'form_16', 'label' => 'Form 16 (For Salaried)', 'required' => false, 'mimes' => ['pdf']],

            ['name' => 'form_26as', 'label' => 'Form 26AS / AIS', 'required' => false, 'mimes' => ['pdf']],

            ['name' => 'bank_statement', 'label' => 'Bank Statement', 'required' => false, 'mimes' => ['pdf']],

            ['name' => 'profit_loss_statement', 'label' => 'Profit & Loss Statement (For Business)', 'required' => false, 'mimes' => ['pdf', 'xls', 'xlsx']],

            ['name' => 'balance_sheet', 'label' => 'Balance Sheet (If Applicable)', 'required' => false, 'mimes' => ['pdf', 'xls', 'xlsx']],

            ['name' => 'other_documents', 'label' => 'Other Supporting Documents', 'required' => false, 'mimes' => ['pdf', 'jpg', 'png']],

        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | 5. GST REGISTRATION
    |--------------------------------------------------------------------------
    */
    'gst-registration'                     => [

        'label'     => 'GST Registration',

        'sections'  => [

            'applicant_details' => [
                'label'  => 'Applicant Details',
                'fields' => [

                    ['name' => 'applicant_name', 'label' => 'Applicant Name (As per PAN)', 'type' => 'text', 'required' => true, 'validation' => 'required|string|max:255'],

                    ['name' => 'father_name', 'label' => 'Father Name', 'type' => 'text', 'required' => true, 'validation' => 'required|string|max:255'],

                    ['name' => 'pan_number', 'label' => 'PAN Number', 'type' => 'text', 'required' => true, 'validation' => 'required|string|size:10|regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/'],

                    ['name' => 'aadhaar_number', 'label' => 'Aadhaar Number', 'type' => 'text', 'required' => true, 'validation' => 'required|digits:12'],

                    ['name' => 'date_of_birth', 'label' => 'Date of Birth', 'type' => 'date', 'required' => true, 'validation' => 'required|date'],

                    ['name' => 'mobile', 'label' => 'Mobile Number', 'type' => 'text', 'required' => true, 'validation' => 'required|digits:10'],

                    ['name' => 'email', 'label' => 'Email Address', 'type' => 'email', 'required' => true, 'validation' => 'required|email'],

                    ['name' => 'residential_address', 'label' => 'Residential Address (As per Aadhaar)', 'type' => 'textarea', 'required' => true, 'validation' => 'required|string'],

                ],
            ],

            'business_details'  => [
                'label'  => 'Business Details',
                'fields' => [

                    ['name' => 'legal_name_of_business', 'label' => 'Legal Name of Business (As per PAN)', 'type' => 'text', 'required' => true, 'validation' => 'required|string|max:255'],

                    ['name' => 'trade_name', 'label' => 'Trade Name (If Any)', 'type' => 'text', 'required' => false, 'validation' => 'nullable|string|max:255'],

                    [
                        'name'       => 'constitution_of_business',
                        'label'      => 'Constitution of Business',
                        'type'       => 'select',
                        'required'   => true,
                        'validation' => 'required|string|in:proprietorship,partnership,private_limited,public_limited,llp,huf,society,trust,other',
                        'options'    => [
                            'proprietorship'  => 'Proprietorship',
                            'partnership'     => 'Partnership',
                            'private_limited' => 'Private Limited Company',
                            'public_limited'  => 'Public Limited Company',
                            'llp'             => 'LLP',
                            'huf'             => 'HUF',
                            'society'         => 'Society',
                            'trust'           => 'Trust',
                            'other'           => 'Other',
                        ],
                    ],

                    ['name' => 'business_commencement_date', 'label' => 'Business Commencement Date', 'type' => 'date', 'required' => true, 'validation' => 'required|date'],

                    ['name' => 'business_address', 'label' => 'Principal Place of Business Address', 'type' => 'textarea', 'required' => true, 'validation' => 'required|string'],

                    [
                        'name'       => 'type_of_property',
                        'label'      => 'Type of Business Property',
                        'type'       => 'select',
                        'required'   => true,
                        'validation' => 'required|string|in:own,rented,leased',
                        'options'    => [
                            'own'    => 'Own',
                            'rented' => 'Rented',
                            'leased' => 'Leased',
                        ],
                    ],

                    ['name' => 'nature_of_business_activity', 'label' => 'Nature of Business Activities', 'type' => 'textarea', 'required' => true, 'validation' => 'required|string'],

                ],
            ],

            'bank_details'      => [
                'label'  => 'Bank Details',
                'fields' => [

                    ['name' => 'bank_account_number', 'label' => 'Bank Account Number', 'type' => 'text', 'required' => true, 'validation' => 'required|string'],

                    ['name' => 'ifsc_code', 'label' => 'IFSC Code', 'type' => 'text', 'required' => true, 'validation' => 'required|string'],

                ],
            ],

        ],

        'documents' => [

            ['name' => 'pan_card', 'label' => 'PAN Card Copy', 'required' => true, 'mimes' => ['pdf', 'jpg', 'png']],

            ['name' => 'aadhaar_card', 'label' => 'Aadhaar Card Copy', 'required' => true, 'mimes' => ['pdf', 'jpg', 'png']],

            ['name' => 'passport_photo', 'label' => 'Passport Size Photograph', 'required' => true, 'mimes' => ['jpg', 'png']],

            ['name' => 'address_proof', 'label' => 'Address Proof (Electricity Bill / Rent Agreement / Property Tax Receipt)', 'required' => true, 'mimes' => ['pdf', 'jpg', 'png']],

            ['name' => 'cancelled_cheque', 'label' => 'Cancelled Cheque / Bank Passbook Copy', 'required' => true, 'mimes' => ['pdf', 'jpg', 'png']],

            ['name' => 'authorization_letter', 'label' => 'Authorization Letter / Board Resolution (If Applicable)', 'required' => false, 'mimes' => ['pdf']],

        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | 6. GST RETURN
    |--------------------------------------------------------------------------
    */
    'gst-return-filing'                    => [

        'label'     => 'GST Return Filing (GSTR-1 / GSTR-3B)',

        'sections'  => [

            'basic_details'          => [
                'label'  => 'Basic Details',
                'fields' => [

                    ['name' => 'contact_person', 'label' => 'Contact Person Name', 'type' => 'text', 'required' => true, 'validation' => 'required|string|max:255'],

                    ['name' => 'mobile', 'label' => 'Mobile Number', 'type' => 'text', 'required' => true, 'validation' => 'required|digits:10'],

                    ['name' => 'email', 'label' => 'Email Address', 'type' => 'email', 'required' => true, 'validation' => 'required|email'],

                ],
            ],

            'business_details'       => [
                'label'  => 'Business Details',
                'fields' => [

                    ['name' => 'firm_name', 'label' => 'Firm / Trade Name', 'type' => 'text', 'required' => true, 'validation' => 'required|string|max:255'],

                    ['name' => 'pan_number', 'label' => 'PAN Number', 'type' => 'text', 'required' => true, 'validation' => 'required|string|size:10|regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/'],

                    ['name' => 'gst_number', 'label' => 'GST Number (GSTIN)', 'type' => 'text', 'required' => true, 'validation' => 'required|string|size:15|regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[A-Z1-9]{1}Z[0-9A-Z]{1}$/'],

                    [
                        'name'       => 'gst_type',
                        'label'      => 'GST Type',
                        'type'       => 'select',
                        'required'   => true,
                        'validation' => 'required|string|in:regular,composition',
                        'options'    => [
                            'regular'     => 'Regular',
                            'composition' => 'Composition',
                        ],
                    ],

                    [
                        'name'       => 'annual_turnover_range',
                        'label'      => 'Annual Turnover Range',
                        'type'       => 'select',
                        'required'   => true,
                        'validation' => 'required|string|in:upto_1_5,upto_5,upto_20,above_20',
                        'options'    => [
                            'upto_1_5' => 'Up to ₹1.5 Cr',
                            'upto_5'   => '₹1.5 Cr – ₹5 Cr',
                            'upto_20'  => '₹5 Cr – ₹20 Cr',
                            'above_20' => 'Above ₹20 Cr',
                        ],
                    ],

                ],
            ],

            'return_details'         => [
                'label'  => 'Return Filing Details',
                'fields' => [

                    ['name' => 'financial_year', 'label' => 'Financial Year', 'type' => 'text', 'required' => true, 'validation' => 'required|string'],

                    ['name' => 'return_period', 'label' => 'Return Period (Month / Quarter)', 'type' => 'text', 'required' => true, 'validation' => 'required|string'],

                    [
                        'name'       => 'frequency_of_return',
                        'label'      => 'Frequency of Return',
                        'type'       => 'select',
                        'required'   => true,
                        'validation' => 'required|string|in:monthly,quarterly',
                        'options'    => [
                            'monthly'   => 'Monthly',
                            'quarterly' => 'Quarterly',
                        ],
                    ],

                ],
            ],

            // 'portal_details'         => [
            //     'label'  => 'GST Portal Login Details',
            //     'fields' => [

            //         ['name' => 'gst_portal_login_id', 'label' => 'GST Portal Login ID', 'type' => 'text', 'required' => true, 'validation' => 'required|string'],

            //         ['name' => 'gst_portal_password', 'label' => 'GST Portal Login Password', 'type' => 'password', 'required' => true, 'validation' => 'required|string'],

            //     ],
            // ],

            'additional_information' => [
                'label'  => 'Additional Information',
                'fields' => [

                    ['name' => 'remarks', 'label' => 'Remarks', 'type' => 'textarea', 'required' => false, 'validation' => 'nullable|string'],

                ],
            ],

        ],

        'documents' => [

            ['name' => 'sales_data', 'label' => 'Sales Data (Excel / JSON / PDF)', 'required' => true, 'mimes' => ['xls', 'xlsx', 'pdf']],

            ['name' => 'purchase_data', 'label' => 'Purchase Data (If Required)', 'required' => false, 'mimes' => ['xls', 'xlsx', 'pdf']],

            ['name' => 'previous_return_copy', 'label' => 'Previous Return Copy (If Applicable)', 'required' => false, 'mimes' => ['pdf']],

            ['name' => 'other_documents', 'label' => 'Other Supporting Documents', 'required' => false, 'mimes' => ['pdf', 'jpg', 'png']],

        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | 7. PRIVATE LIMITED REGISTRATION
    |--------------------------------------------------------------------------
    */
    'private-limited-company-registration' => [

        'label'     => 'Private Limited Company Registration',

        'sections'  => [

            'company_details'      => [
                'label'  => 'Company Details',
                'fields' => [

                    ['name' => 'proposed_company_name', 'label' => 'Proposed Company Name (Up to 2-3 options)', 'type' => 'text', 'required' => true, 'validation' => 'required|string|max:255'],

                    ['name' => 'main_object', 'label' => 'Main Object / Business Activity', 'type' => 'textarea', 'required' => true, 'validation' => 'required|string'],

                    ['name' => 'authorized_capital', 'label' => 'Authorized Share Capital', 'type' => 'number', 'required' => true, 'validation' => 'required|numeric|min:100000'],

                    ['name' => 'paidup_capital', 'label' => 'Paid-up Share Capital', 'type' => 'number', 'required' => true, 'validation' => 'required|numeric|min:1'],

                    ['name' => 'registered_office_address', 'label' => 'Registered Office Address', 'type' => 'textarea', 'required' => true, 'validation' => 'required|string'],

                    ['name' => 'state', 'label' => 'State', 'type' => 'text', 'required' => true, 'validation' => 'required|string'],

                    ['name' => 'district', 'label' => 'District', 'type' => 'text', 'required' => true, 'validation' => 'required|string'],

                ],
            ],

            'director_details'     => [
                'label'  => 'Director Details',
                'fields' => [

                    ['name' => 'number_of_directors', 'label' => 'Number of Directors (Minimum 2)', 'type' => 'number', 'required' => true, 'validation' => 'required|integer|min:2'],

                    [
                        'name'       => 'resident_director',
                        'label'      => 'At least one Director Resident in India?',
                        'type'       => 'select',
                        'required'   => true,
                        'validation' => 'required|string|in:yes,no',
                        'options'    => [
                            'yes' => 'Yes',
                            'no'  => 'No',
                        ],
                    ],

                    [
                        'name'       => 'director_din_available',
                        'label'      => 'Do Directors Have DIN?',
                        'type'       => 'select',
                        'required'   => true,
                        'validation' => 'required|string|in:yes,no',
                        'options'    => [
                            'yes' => 'Yes',
                            'no'  => 'No',
                        ],
                    ],

                ],
            ],

            'shareholding_details' => [
                'label'  => 'Shareholding Details',
                'fields' => [

                    ['name' => 'number_of_shareholders', 'label' => 'Number of Shareholders', 'type' => 'number', 'required' => true, 'validation' => 'required|integer|min:1'],

                    ['name' => 'shareholding_pattern', 'label' => 'Shareholding Pattern (Describe % distribution)', 'type' => 'textarea', 'required' => true, 'validation' => 'required|string'],

                ],
            ],

            'contact_details'      => [
                'label'  => 'Contact Details',
                'fields' => [

                    ['name' => 'contact_person', 'label' => 'Contact Person Name', 'type' => 'text', 'required' => true, 'validation' => 'required|string|max:255'],

                    ['name' => 'mobile', 'label' => 'Mobile Number', 'type' => 'text', 'required' => true, 'validation' => 'required|digits:10'],

                    ['name' => 'email', 'label' => 'Email Address', 'type' => 'email', 'required' => true, 'validation' => 'required|email'],

                ],
            ],

        ],

        'documents' => [

            ['name' => 'director_pan', 'label' => 'PAN Card of Directors', 'required' => true, 'mimes' => ['pdf', 'jpg', 'png']],

            ['name' => 'director_address_proof', 'label' => 'Address Proof of Directors', 'required' => true, 'mimes' => ['pdf', 'jpg', 'png']],

            ['name' => 'passport_photo', 'label' => 'Passport Size Photograph of Directors', 'required' => true, 'mimes' => ['jpg', 'png']],

            ['name' => 'registered_office_proof', 'label' => 'Registered Office Address Proof (Electricity Bill / Rent Agreement)', 'required' => true, 'mimes' => ['pdf', 'jpg', 'png']],

            ['name' => 'noc_from_owner', 'label' => 'NOC from Property Owner (If Rented)', 'required' => false, 'mimes' => ['pdf']],

            ['name' => 'moa_draft', 'label' => 'Draft MOA', 'required' => true, 'mimes' => ['pdf']],

            ['name' => 'aoa_draft', 'label' => 'Draft AOA', 'required' => true, 'mimes' => ['pdf']],

        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | 8. PARTNERSHIP FIRM REGISTRATION
    |--------------------------------------------------------------------------
    */
    'partnership-firm-registration'        => [

        'label'     => 'Partnership Firm Registration',

        'sections'  => [

            'firm_details'    => [
                'label'  => 'Firm Details',
                'fields' => [

                    ['name' => 'firm_name', 'label' => 'Proposed Firm Name', 'type' => 'text', 'required' => true, 'validation' => 'required|string|max:255'],

                    ['name' => 'nature_of_business', 'label' => 'Nature of Business Activity', 'type' => 'textarea', 'required' => true, 'validation' => 'required|string'],

                    ['name' => 'business_commencement_date', 'label' => 'Date of Commencement of Business', 'type' => 'date', 'required' => true, 'validation' => 'required|date'],

                    ['name' => 'principal_place_of_business', 'label' => 'Principal Place of Business Address', 'type' => 'textarea', 'required' => true, 'validation' => 'required|string'],

                    ['name' => 'state', 'label' => 'State', 'type' => 'text', 'required' => true, 'validation' => 'required|string'],

                    ['name' => 'district', 'label' => 'District', 'type' => 'text', 'required' => true, 'validation' => 'required|string'],

                ],
            ],

            'partner_details' => [
                'label'  => 'Partner Details',
                'fields' => [

                    ['name' => 'number_of_partners', 'label' => 'Number of Partners (Minimum 2)', 'type' => 'number', 'required' => true, 'validation' => 'required|integer|min:2'],

                    ['name' => 'capital_contribution', 'label' => 'Capital Contribution Details', 'type' => 'textarea', 'required' => true, 'validation' => 'required|string|max:255'],

                    ['name' => 'profit_sharing_ratio', 'label' => 'Profit Sharing Ratio (%)', 'type' => 'textarea', 'required' => true, 'validation' => 'required|string|max:255'],

                ],
            ],

            'contact_details' => [
                'label'  => 'Contact Details',
                'fields' => [

                    ['name' => 'contact_person', 'label' => 'Contact Person Name', 'type' => 'text', 'required' => true, 'validation' => 'required|string|max:255'],

                    ['name' => 'mobile', 'label' => 'Mobile Number', 'type' => 'text', 'required' => true, 'validation' => 'required|digits:10'],

                    ['name' => 'email', 'label' => 'Email Address', 'type' => 'email', 'required' => true, 'validation' => 'required|email'],

                ],
            ],

        ],

        'documents' => [

            ['name' => 'partner_pan', 'label' => 'PAN Card of Partners', 'required' => true, 'mimes' => ['pdf', 'jpg', 'png']],

            ['name' => 'partner_address_proof', 'label' => 'Address Proof of Partners', 'required' => true, 'mimes' => ['pdf', 'jpg', 'png']],

            ['name' => 'passport_photo', 'label' => 'Passport Size Photo of Partners', 'required' => true, 'mimes' => ['jpg', 'png']],

            ['name' => 'business_address_proof', 'label' => 'Business Address Proof (Electricity Bill / Rent Agreement)', 'required' => true, 'mimes' => ['pdf', 'jpg', 'png']],

            ['name' => 'noc_from_owner', 'label' => 'NOC from Property Owner (If Rented)', 'required' => false, 'mimes' => ['pdf']],

            ['name' => 'partnership_deed_draft', 'label' => 'Draft Partnership Deed', 'required' => true, 'mimes' => ['pdf']],

        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | 9. MSME / UDYAM REGISTRATION
    |--------------------------------------------------------------------------
    */
    'msme-udyam-registration'              => [

        'label'     => 'MSME / Udyam Registration',

        'sections'  => [

            'aadhaar_details'           => [
                'label'  => 'Aadhaar & Applicant Details',
                'fields' => [

                    ['name' => 'aadhaar_number', 'label' => 'Aadhaar Number (Of Proprietor / Managing Partner / Director)', 'type' => 'text', 'required' => true, 'validation' => 'required|digits:12'],

                    ['name' => 'applicant_name', 'label' => 'Name as per Aadhaar', 'type' => 'text', 'required' => true, 'validation' => 'required|string|max:255'],

                    ['name' => 'mobile', 'label' => 'Mobile Number (Linked with Aadhaar)', 'type' => 'text', 'required' => true, 'validation' => 'required|digits:10'],

                    ['name' => 'email', 'label' => 'Email Address', 'type' => 'email', 'required' => true, 'validation' => 'required|email'],

                ],
            ],

            'enterprise_details'        => [
                'label'  => 'Enterprise Details',
                'fields' => [

                    [
                        'name'       => 'organisation_type',
                        'label'      => 'Type of Organisation',
                        'type'       => 'select',
                        'required'   => true,
                        'validation' => 'required|string|in:proprietorship,partnership,private_limited,public_limited,llp,huf,society,trust,other',
                        'options'    => [
                            'proprietorship'  => 'Proprietorship',
                            'partnership'     => 'Partnership',
                            'private_limited' => 'Private Limited Company',
                            'public_limited'  => 'Public Limited Company',
                            'llp'             => 'LLP',
                            'huf'             => 'HUF',
                            'society'         => 'Society',
                            'trust'           => 'Trust',
                            'other'           => 'Other',
                        ],
                    ],

                    ['name' => 'enterprise_name', 'label' => 'Name of Enterprise / Business', 'type' => 'text', 'required' => true, 'validation' => 'required|string|max:255'],

                    ['name' => 'pan_number', 'label' => 'PAN of Enterprise', 'type' => 'text', 'required' => true, 'validation' => 'required|string|size:10|regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/'],

                    ['name' => 'gst_number', 'label' => 'GSTIN (If Available)', 'type' => 'text', 'required' => false, 'validation' => 'nullable|string|size:15|regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[A-Z1-9]{1}Z[0-9A-Z]{1}$/'],

                    ['name' => 'business_address', 'label' => 'Business Address', 'type' => 'textarea', 'required' => true, 'validation' => 'required|string'],

                    ['name' => 'state', 'label' => 'State', 'type' => 'text', 'required' => true, 'validation' => 'required|string'],

                    ['name' => 'district', 'label' => 'District', 'type' => 'text', 'required' => true, 'validation' => 'required|string'],

                ],
            ],

            'business_activity_details' => [
                'label'  => 'Business Activity Details',
                'fields' => [

                    [
                        'name'       => 'major_activity',
                        'label'      => 'Major Activity',
                        'type'       => 'select',
                        'required'   => true,
                        'validation' => 'required|string|in:manufacturing,services',
                        'options'    => [
                            'manufacturing' => 'Manufacturing',
                            'services'      => 'Services',
                        ],
                    ],

                    ['name' => 'nic_code', 'label' => 'NIC Code (If Known)', 'type' => 'text', 'required' => false, 'validation' => 'nullable|string|max:10'],

                    ['name' => 'number_of_employees', 'label' => 'Number of Employees', 'type' => 'number', 'required' => true, 'validation' => 'required|integer|min:0'],

                    ['name' => 'investment_amount', 'label' => 'Investment in Plant & Machinery / Equipment (₹)', 'type' => 'number', 'required' => true, 'validation' => 'required|numeric|min:0'],

                    ['name' => 'annual_turnover', 'label' => 'Annual Turnover (₹)', 'type' => 'number', 'required' => true, 'validation' => 'required|numeric|min:0'],

                ],
            ],

            'bank_details'              => [
                'label'  => 'Bank Details',
                'fields' => [

                    ['name' => 'bank_account_number', 'label' => 'Bank Account Number', 'type' => 'text', 'required' => true, 'validation' => 'required|string'],

                    ['name' => 'ifsc_code', 'label' => 'IFSC Code', 'type' => 'text', 'required' => true, 'validation' => 'required|string'],

                ],
            ],

        ],

        'documents' => [

            ['name' => 'aadhaar_card', 'label' => 'Aadhaar Card Copy', 'required' => true, 'mimes' => ['pdf', 'jpg', 'png']],

            ['name' => 'pan_card', 'label' => 'PAN Card Copy', 'required' => true, 'mimes' => ['pdf', 'jpg', 'png']],

            ['name' => 'gst_certificate', 'label' => 'GST Certificate (If Applicable)', 'required' => false, 'mimes' => ['pdf']],

            ['name' => 'bank_proof', 'label' => 'Cancelled Cheque / Bank Passbook Copy', 'required' => true, 'mimes' => ['pdf', 'jpg', 'png']],

        ],

    ],

];
