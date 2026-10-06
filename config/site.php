<?php

/*
|--------------------------------------------------------------------------
| Event Experts Ltd — site content
|--------------------------------------------------------------------------
| Everything the team is likely to change (contact details, services,
| form options) lives here so the views and the forms stay in sync.
| Wording is drawn from the company's own Instagram and Facebook pages.
*/

return [

    'name' => 'Event Experts Ltd',
    'short' => 'Event Experts',
    'descriptor' => 'Wedding and Event Decor',
    'tagline' => 'You dream it, we make it happen.',
    'intro' => 'Event decor, management and event rental company with 20 years of experience. Let our innovative concepts and unique designs make your dream event a reality.',
    'years' => 20,

    'phone' => '(868) 473-4224',
    'phone_href' => '+18684734224',
    'phone_alt' => '(868) 686-3724',
    'phone_alt_href' => '+18686863724',
    'whatsapp' => '18684734224',
    'email' => 'eventexpertsltd@gmail.com',

    'location' => 'Chaguanas, Trinidad and Tobago',
    'warehouse' => 'Bejucal, Chaguanas',
    'timezone' => 'America/Port_of_Spain',

    'links' => [
        'instagram' => 'https://www.instagram.com/eventexpertsltd/',
        'facebook' => 'https://www.facebook.com/EventExpertsTT/',
    ],

    // Source: Instagram and Facebook profiles, October 2026.
    'social' => [
        'instagram_handle' => '@eventexpertsltd',
        'instagram_followers' => 11000,
        'facebook_followers' => 14000,
    ],

    'services' => [
        'weddings' => [
            'name' => 'Luxury Weddings',
            'icon' => 'gem',
            'eyebrow' => 'Design & decor',
            'summary' => 'Full wedding design, from the entrance to the last table.',
            'intro' => 'Floral overheads, stage design, custom glass tables and furniture, mandaps and petal walkways. We design the whole room around the two of you, then build it with a team that has done this for twenty years.',
            'image' => 'images/portfolio/sime-christian-1.jpg',
            'topic' => 'Luxury wedding',
            'points' => [
                ['flower-2', 'Floral overheads', 'Suspended florals and ceiling installations, including the largest floral overhead we have ever built.'],
                ['theater', 'Stage and entrance design', 'Head-table stages, backdrops, arches and entrances that set the tone the moment guests arrive.'],
                ['armchair', 'Custom tables and furniture', 'Glass tables, seating and table pieces made and styled for your colour story.'],
                ['sparkles', 'Hindu and traditional ceremonies', 'Mandaps, haldi nights and vivaah sanskar decor that mix the modern with the traditional.'],
            ],
            'categories' => ['wedding', 'cultural'],
        ],
        'micro-weddings' => [
            'name' => 'Micro Weddings',
            'icon' => 'home',
            'eyebrow' => 'Intimate celebrations',
            'summary' => 'Your backyard or living room, transformed into a venue.',
            'intro' => 'A micro wedding does not mean compromising on style. We can take any room of your home, or your backyard, and transform it into a venue for an intimate wedding, birthday or any event.',
            'image' => 'images/portfolio/micro-weddings-1.jpg',
            'topic' => 'Micro wedding',
            'points' => [
                ['home', 'Home and backyard transformations', 'Entrances, gazebos, ceremony set-ups and reception tables built where you live.'],
                ['flower-2', 'Big style, small guest list', 'Glass tables with floral arrangements for that wow factor at a smaller scale.'],
                ['heart', 'Ceremony and reception', 'One team for both parts of the day, so the look carries through.'],
            ],
            'categories' => ['micro'],
        ],
        'corporate' => [
            'name' => 'Corporate & State Events',
            'icon' => 'building-2',
            'eyebrow' => 'Brands & institutions',
            'summary' => 'Decor for launches, premium fetes and national occasions.',
            'intro' => 'From the Hyatt Lime to the re-opening of the Red House and the first sitting of Parliament, we deliver decor for occasions where the details are noticed and the deadline does not move.',
            'image' => 'images/portfolio/hyatt-lime-1.jpg',
            'topic' => 'Corporate event',
            'points' => [
                ['party-popper', 'Premium fetes and brand events', 'Luxurious decor for all-inclusive events, working alongside lighting, sound and production teams.'],
                ['landmark', 'State and ceremonial occasions', 'Formal decor for official openings and sittings.'],
                ['users', 'One accountable team', 'Design, warehouse and on-site crew under one roof.'],
            ],
            'categories' => ['corporate'],
        ],
        'parties' => [
            'name' => 'Parties & Celebrations',
            'icon' => 'cake',
            'eyebrow' => 'Milestones',
            'summary' => 'Birthdays, anniversaries and celebrations worth dressing up for.',
            'intro' => 'Milestone birthdays, anniversaries, haldi nights and family celebrations get the same care as a wedding: a design that suits the guest of honour and a room that photographs beautifully.',
            'image' => 'images/portfolio/kris-65th-1.jpg',
            'topic' => 'Party or celebration',
            'points' => [
                ['cake', 'Milestone birthdays', 'From intimate dinners to ballroom celebrations.'],
                ['palette', 'Themed design', 'Colour stories and themes carried through backdrops, tables and florals.'],
                ['utensils', 'Tablescapes', 'Table pieces, chargers and centrepieces styled for your guest count.'],
            ],
            'categories' => ['party'],
        ],
        'rentals' => [
            'name' => 'Event Rentals',
            'icon' => 'warehouse',
            'eyebrow' => 'From our warehouse',
            'summary' => 'Grass walls, glass tables and decor pieces for hire.',
            'intro' => 'Our Chaguanas warehouse holds the pieces you have seen at our events. Tell us what you need and your date, and we will confirm what is available.',
            'image' => 'images/portfolio/jodel-tish-4.jpg',
            'topic' => 'Event rentals',
            'points' => [
                ['trees', 'Grass walls and backdrops', 'Photo backdrops and feature walls for any occasion.'],
                ['armchair', 'Tables and furniture', 'Glass tables and statement furniture from our own stock.'],
                ['lamp', 'Table pieces and decor', 'Centrepieces and decor items to finish the room.'],
            ],
            'categories' => [],
        ],
    ],

    'categories' => [
        'wedding' => 'Weddings',
        'cultural' => 'Hindu & traditional',
        'micro' => 'Micro weddings',
        'corporate' => 'Corporate & state',
        'party' => 'Parties',
    ],

    'process' => [
        ['message-circle', 'Tell us about your day', 'Send your date, venue and guest count through the planner, or call or WhatsApp us.'],
        ['pen-tool', 'We design the concept', 'We guide you from what you love to a design that works in your venue.'],
        ['package-check', 'Our team prepares', 'Months of planning, ordering, designing and prepping happen in our warehouse.'],
        ['sparkles', 'We build it on the day', 'Our crew transforms the space, then takes it all down afterwards.'],
    ],

    // A client review the company shared on Instagram (January 2020).
    'review' => [
        'quote' => 'Everything went PERFECT. Thank you so much and your team, everything was beyond what I could imagine. The decor was amazing, everyone loved it. I don\'t have any complaints whatsoever, everything was just perfect.',
        'source' => 'Client review, shared on Instagram',
    ],

    // Planners, venues and suppliers credited alongside the company in its own posts.
    'collaborators' => [
        'Hyatt Regency Trinidad', 'Events by Tricia', 'Laura Darwent Weddings', 'The Event Element', 'Lollabee Group',
        'Premium Events Services', 'Lazaabs Lighting SFX', 'Valentine\'s Floral Designs', 'Awesome Photography',
        'Luvo Weddings', 'Yaisa Tangwell Photography', 'Safran Patisserie', 'Rink Films',
    ],

    // Options for the event planner form.
    'event_types' => ['Wedding', 'Micro wedding', 'Hindu wedding ceremonies', 'Corporate event', 'Birthday or anniversary', 'Other celebration'],
    'guest_ranges' => ['Up to 50', '50 – 150', '150 – 300', '300 – 500', 'More than 500'],
    'needs' => ['Full decor and design', 'Floral overhead', 'Stage or backdrop', 'Tables and tablescapes', 'Ceremony set-up', 'Event rentals only', 'Event management'],
    'contact_methods' => ['WhatsApp', 'Phone call', 'Email'],
    'heard' => ['Instagram', 'Facebook', 'A friend or family member', 'I was a guest at one of your events', 'A planner or venue', 'Other'],
    'topics' => ['General enquiry', 'Luxury wedding', 'Micro wedding', 'Corporate event', 'Party or celebration', 'Event rentals', 'Careers'],

];
