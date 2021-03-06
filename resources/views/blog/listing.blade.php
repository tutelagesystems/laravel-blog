@extends('layout.master')

@section('content')

    <header class="blog-post">
        <div class="container">
            <div class="col-lg-12 text-center">
                <h1>All Blog Posts</h1>
                <hr class="star-primary">
            </div>
        </div>
    </header>

    <!-- Blog Grid Section -->
    <section id="blog" class="no-padding padding-bottom-20">
        <div class="container">
            @foreach($posts as $post)
                <div style="border-bottom: 1px solid #f1f1f1;">
                    <div class="row">
                        <div class="col-sm-12 padding-bottom-20">
                            <h4>
                                <a href="{{ URL::route('blog.view', $post->slug) }}">
                                    {{ $post->title }}
                                </a>
                                <small>{{ date('F d, Y g:i A', strtotime($post->published_at)) }} ({{ $post->published_at->diffForHumans() }})</small>
                            </h4>
                            {!! str_limit(strip_tags($post->content, '<br>'), 300) !!}
                            <div>
                                <a href="{{ URL::route('blog.view', $post->slug) }}" class="read-more">
                                    read more <i class="fa fa-arrow-circle-o-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>
    </section>

@stop