@extends('frontend.layouts.app')

@section('meta_title'){{ $page->meta_title }}@stop

@section('meta_description'){{ $page->meta_description }}@stop

@section('meta_keywords'){{ $page->tags }}@stop

@section('meta')
    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="{{ $page->meta_title }}">
    <meta itemprop="description" content="{{ $page->meta_description }}">
    <meta itemprop="image" content="{{ uploaded_asset($page->meta_image) }}">

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="website">
    <meta name="twitter:site" content="@publisher_handle">
    <meta name="twitter:title" content="{{ $page->meta_title }}">
    <meta name="twitter:description" content="{{ $page->meta_description }}">
    <meta name="twitter:creator" content="@author_handle">
    <meta name="twitter:image" content="{{ uploaded_asset($page->meta_image) }}">

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ $page->meta_title }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ URL($page->slug) }}" />
    <meta property="og:image" content="{{ uploaded_asset($page->meta_image) }}" />
    <meta property="og:description" content="{{ $page->meta_description }}" />
    <meta property="og:site_name" content="{{ env('APP_NAME') }}" />
@endsection

@section('content')
<section class="pt-4 mb-4">
    <div class="container text-center">
        <div class="row">
            <div class="col-lg-6 text-center text-lg-left">
                <h1 class="fw-600 h4">{{ $page->getTranslation('title') }}</h1>
            </div>
            <div class="col-lg-6">
                <ul class="breadcrumb bg-transparent p-0 justify-content-center justify-content-lg-end">
                    <li class="breadcrumb-item has-transition opacity-50 hov-opacity-100">
                        <a class="text-reset" href="{{ route('home') }}">{{ translate('Home')}}</a>
                    </li>
                    <li class="text-dark fw-600 breadcrumb-item">
                        "{{ translate('Privacy Policy') }}"
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>
<section class="mb-4">
    <div class="container">
        <div class="p-4 bg-white rounded shadow-sm overflow-hidden mw-100 text-left">
            @if(0)
            @php
                echo $page->getTranslation('content');
            @endphp
            @endif
            
<p>We know that you care how information about you is used and shared, and we appreciate your trust that we will do so
    carefully and sensibly. This Privacy Notice describes how we collect and process your personal information through
    our websites, devices, products, services, online marketplace and applications that reference this Privacy Notice.
    By using our Services you agree to our use of your personal information (including sensitive personal information)
    in accordance with this Privacy Notice, as may be amended from time to time by us at our discretion. You also agree
    and consent to us collecting, storing, processing, transferring, and sharing your personal information (including
    sensitive personal information) with third parties or service providers for the purposes set out in this Privacy
    Notice.
</p>

<b>What Personal Information About Customers Do We Collect?
</b>

<p>We collect your personal information in order to provide and continually improve our products and services. <br>
    Here are the types of personal information we collect:

   <br> <b>Information You Give Us:</b> We receive and store any information you provide in relation to our Services. You
    can choose not to provide certain information, but then you might not be able to take advantage of many of our
    Services.
    <br><b>Automatic Information:</b> We automatically collect and store certain types of information about your use of our
    Services, including information about your interaction with content and services available through our Services.
    Like many websites, we use cookies and other unique identifiers, and we obtain certain types of information when
    your web browser or device accesses our Services and other content served by or on behalf of us on other websites.
    <br><b>Information from Other Sources:</b> We might receive information about you from other sources, such as updated
    delivery and address information from our carriers, which we use to correct our records and deliver your next
    purchase more easily.
</p>

<b>For What Purposes Do We Use Your Personal Information?
</b>

<p>
    We use your personal information to operate, provide, develop, and improve the products and services that we offer
    our customers. These purposes include:
    <b>Purchase and delivery of products and services. </b>We use your personal information to take
    and fulfil orders, deliver products and services, process payments, and communicate with you about orders, products
    and services, and promotional offers.

<ul>
    <li><b>Provide, troubleshoot, and improve our Services.</b> We use your personal information to provide
        functionality, analyse performance, fix errors, and improve the usability and effectiveness of our Services.
    </li>
    <li><b>Recommendations and personalization.</b> We use your personal information to recommend features, products,
        and services that might be of interest to you, identify your preferences, and personalize your experience with
        our Services.
    </li>
    <li><b>Provide voice, image and camera services.</b> When you use our voice, image and camera services, we use your
        voice input, images, videos, and other personal information to respond to your requests, provide the requested
        service to you, and improve our services.
    </li>
    <li><b>Comply with legal obligations.</b> In certain cases, we collect and use your personal information to comply
        with laws. For instance, we collect from sellers ‘information regarding place of establishment and bank account
        information for identity verification and other purposes.</li>
    <li><b>Communicate with you.</b> We use your personal information to communicate with you in relation to our
        Services via different channels (e.g., by phone, e-mail, chat).
    </li>
    <li><b>Advertising.</b> We use your personal information to display interest-based ads for features, products, and
        services that might be of interest to you. We do not use information that personally identifies you to display
        interest-based ads
    </li>
    <li><b>Fraud Prevention and Credit Risks.</b> We use personal information to prevent and detect fraud and abuse in
        order to protect the security of our customers and others. We may also use scoring methods to assess and manage
        credit risks.
    </li>
</ul>

</p>

<b>What About Cookies and Other Identifiers?
</b>

<ul>
    <li>To enable our systems to recognize your browser or device and to provide and improve our Services, we use
        cookies and other identifiers.
    </li>
</ul>

<b>Do We Share Your Personal Information?
</b>

<p>Information about our customers is an important part of our business and we are not in the business of selling our customers’ personal information to others. We share customers’ personal information only as described below and controls that either are subject to this Privacy Notice or follow practices at least as protective as those described in this Privacy Notice.
</p>

<ul>
    <li><b>Transactions involving Third Parties: </b>We make available to you services, products, applications, or skills provided by third parties for use on or through our Services. </li>
    <li><b>Third-Party Service Providers:</b>We employ other companies and individuals to perform functions on our behalf. Examples include fulfilling orders for products or services, delivering packages, sending postal mail and e-mail, removing repetitive information from customer lists, analyzing data, providing marketing assistance, providing search results and links (including paid listings and links), processing payments, transmitting content, scoring, assessing and managing credit risk, and providing customer service. These third-party service providers have access to personal information needed to perform their functions, but may not use it for other purposes. Further, they must process the personal information in accordance with applicable law.
</li>
    <li><b>Business Transfers:</b>As we continue to develop our business, we might sell or buy other businesses or services. In such transactions, customer information generally is one of the transferred business assets but remains subject to the promises made in any pre-existing Privacy Notice (unless, of course, the customer consents otherwise). Also, in the unlikely event that our company or substantially all of their assets are acquired, customer information will of course be one of the transferred assets.</li>
    <li><b>Protection of Our services and Others:</b>We release account and other personal information when we believe release is appropriate to comply with the law or protect the rights, property, or safety of our company, our users, or others. This includes exchanging information with other companies and organizations for fraud protection and credit risk reduction.
Other than as set out above, you will receive notice when personal information about you might be shared with third parties, and you will have an opportunity to choose not to share the information.
</li>
</ul>

<b>How Secure Is Information About Me?
</b>

<p>We design our systems with your security and privacy in mind.
</p>

<ul>
    <li>We work to protect the security of your personal information during transmission by using encryption protocols and software.
</li>
    <li>We follow the <b> Payment Card Industry Data Security Standard (PCI DSS)</b> when handling payment card data.
</li>
    <li>We maintain physical, electronic, and procedural safeguards in connection with the collection, storage, processing, and disclosure of personal customer information. Our security procedures mean that we may occasionally request proof of identity before we disclose personal information to you.
</li>
    <li>Our devices offer security features to protect them against unauthorized access and loss of data. You can control these features and configure them based on your needs.
</li>
    <li>It is important for you to protect against unauthorized access to your password and to your computers, devices and applications. Be sure to sign off when finished using a shared computer.</li>
</ul>

<b>What About Advertising?
</b>

<b>Third-Party Advertisers and Links to Other Websites: </b>Our Services may include third-party advertising and links to other websites and apps. Third-party advertising partners may collect information about you when you interact with their content, advertising, and services.

<b>Use of Third-Party Advertising Services: </b>We provide ad companies with information that allows them to serve you with more useful and relevant ads and to measure their effectiveness. We never share your name or other information that directly identifies you when we do this. Instead, we use an advertising identifier like a cookie, a device identifier, or a code derived from applying irreversible cryptography to other information like an email address. For example, if you have already downloaded one of our apps, we will share your advertising identifier and data about that event so that you will not be served an ad to download the app again. Some ad companies also use this information to serve you relevant ads from other advertisers.


<b>Are Children Allowed to Use Our Services ?
</b>

<p>We do not sell products for purchase by children. If you are under the age of 18 years, you may use our Services only with the involvement of a parent or guardian.
</p>

<b>Data Retention </b>
<p>We retain your personal information in accordance with applicable laws, for a period no longer than is required for the purpose for which it was collected or as required under any applicable law. However, we may retain data related to you if we believe it may be necessary to prevent fraud or future abuse, to enable us to exercise its legal rights and/or defend against legal claims or if required by law or for other legitimate purposes. We may continue to retain your data in anonymous form for analytical and research purposes.
</p>

<b>Your Rights
</b>

<p>We take every reasonable step to ensure that your personal information that we process is accurate and, where necessary, kept up to date, and any of your personal information that we process that you inform us is inaccurate (having regard to the purposes for which they are processed) is erased or rectified. You may access, correct, and update your personal information directly through the functionalities provided on the Platform. You may delete certain non-mandatory information by logging into our website and visiting Profile and Settings sections. You can also write to us at the contact information provided below to assist you with these requests.
You have an option to withdraw your consent that you have already provided by writing to us at the contact information provided below. Please mention “for withdrawal of consent” in the subject line of your communication. We will verify such requests before acting upon your request. Please note, however, that withdrawal of consent will not be retroactive and will be in accordance with the terms of this Privacy Policy, related Terms of Use and applicable laws. In the event you withdraw consent given to us under this Privacy Policy, such withdrawal may hamper your access to the Platform or restrict provision of our services to you for which we consider that information to be necessary.
</p>

<b>Your Consent
</b>

<p>By visiting our Platform or by providing your information, you consent to the collection, use, storage, disclosure and otherwise processing of your information (including sensitive personal information) on the Platform in accordance with this Privacy Policy. If you disclose to us any personal information relating to other people, you represent that you have the authority to do so and to permit us to use the information in accordance with this Privacy Policy.
You, while providing your personal information over the Platform or any partner platforms or establishments, consent to us to contact you through SMS, instant messaging apps, call and/or e-mail for the purposes specified in this Privacy Policy.
</p>

<b>Changes to this Privacy Policy
</b>

<p>Please check our Privacy Policy periodically for changes. We may update this Privacy Policy to reflect changes to our information practices. We will alert you to significant changes by posting the date our policy got last updated, placing a notice on our Platform, or by sending you an email when we are required to do so by applicable law.

</p>


<b>Grievance Officer
</b>

<p>
    <b>Name-          Ayush Saraf
</b> <br>

<b>Company -   Sunrise Timply Company Pvt ltd
</b><br>
Address-     AMBUJA NEOTIA ECO CENTRE, UNIT-603, EM-4, EM BLOCK SECTOR-V, SALTLAKE CITY, 
                         Kolkata, WB 700091<br>
Contact-           9830749617


</p>


        </div>
    </div>
</section>
@endsection
