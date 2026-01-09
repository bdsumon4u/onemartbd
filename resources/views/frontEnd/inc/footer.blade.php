<footer>
    <div class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="footer-menu">
                        <ul>
                            <li>
                                <a href="{{route('about_us')}}">আমাদের সম্পর্কে</a>
                            </li>
                            <li>
                                <a href="{{route('delivery_policy')}}">ডেলিভারি পলিসি</a>
                            </li>
                            <li>
                                <a href="{{route('return_policy')}}">রিটার্ন পলিসি</a>
                            </li>
                        </ul>
                    </div>

                    <div class="social_links">
                        <ul>
                            <li>
                                <a class="facebook" target="_blank" href="{{$web_settings->website_facebook??""}}"><i class="fa fa-facebook"></i></a>
                            </li>
                            <li>
                                <a class="twitter" target="_blank" href="{{$web_settings->website_twitter??""}}"><i class="fa fa-twitter"></i></a>
                            </li>
                            <li>
                                <a class="instagram" target="_blank" href="{{$web_settings->website_instagram??""}}"><i class="fa fa-instagram"></i></a>
                            </li>
                            <li>
                                <a class="youtube" target="_blank" href="{{$web_settings->website_youtube??""}}"><i class="fa fa-youtube"></i></a>
                            </li>
                        </ul>
                    </div>

                    <div class="copyright_text">
                        <p>{!! $web_settings->website_copyright_text !!}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
