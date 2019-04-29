<?php

// Get Fields gives the fields to get depending on level
// 1 = Developer, 2 = Admin, 3 = User

$get_fields = [
    'clients' => [
        1=>['id','name','owner','users','logo_img','date_added','created_by'],
        2=>false,
        3=>false,
    ],
    'users' => [
        1=>['id','first_name','last_name','email','clients','level','skills','profile_img','token','date_added','created_by'],
        2=>['id','first_name','last_name','email','clients','level','skills','profile_img','token','date_added','created_by'],
        3=>['id','first_name','last_name','email','clients','level','skills','profile_img','token','date_added','created_by'],
    ],
    // 'events' => [
    //     1=>['id','name','venue','eap','start_time','end_time','notes','created_by'],
    //     2=>['id','name','venue','eap','start_time','end_time','notes','created_by'],
    //     3=>['id','name','venue','eap','start_time','end_time','notes','created_by'],
    // ],
    // 'incidents' => [
    //     1=>['id','name','required_skills','preferred_skills','created_by'],
    //     2=>['id','name','required_skills','preferred_skills','created_by'],
    //     3=>['id','name','required_skills','preferred_skills','created_by'],
    //
    // ],
    // 'skills' => [
    //     1=>['id','name','description','client','created_by'],
    //     2=>['id','name','description','client','created_by'],
    //     3=>['id','name','description','client','created_by'],
    // ],
    // 'venues' => [
    //     1=>['id','name','first_line','second_line','city','county','postcode','contact_email','contact_number','created_by'],
    //     2=>['id','name','first_line','second_line','city','county','postcode','contact_email','contact_number','created_by'],
    //     3=>['id','name','first_line','second_line','city','county','postcode','contact_email','contact_number','created_by'],
    // ]
];
