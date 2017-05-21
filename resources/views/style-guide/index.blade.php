@extends('layouts.style-guide')

@section('content')
    <h3 class="section-header primary">Content</h3>

    <div class="section-content">
        <div class="container">
            <h2>Groups</h2>
            <p class="info">
                Groups are used to display items in a grid format. They are fixed width, will wrap, and form to the height of the largest container in the row. Currently groups are used for Piles and Buoys.
            </p>
            <div class="group">
                <div class="group--item">
                    <div class="group--item-heading">
                        <h4>
                            <div class="group--item-heading-name">dev</div>
                            <div class="action-btn">
                                <button class="btn btn-small">
                                    <span class="icon-pencil"></span>
                                </button>
                            </div>
                        </h4>
                    </div>

                    <div class="group--item-content">
                        <h4>Sites</h4>
                        <div class="list">
                            <a href="#" class="list--item">
                                <div class="list--item-name">
                                    dev.codepier.io
                                </div>
                            </a>
                            <a href="#" class="list--item">
                                <div class="list--item-name">
                                    dev.style-guide.codepier.io
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="btn-footer text-center">
                        <button class="btn">Delete</button>
                        <button class="btn">Create Site</button>
                    </div>
                </div>
                <div class="group--item">
                    <div class="group--item-heading">
                        <h4>
                            <div class="group--item-heading-name">qa</div>
                            <div class="action-btn">
                                <button class="btn btn-small">
                                    <span class="icon-pencil"></span>
                                </button>
                            </div>
                        </h4>
                    </div>

                    <div class="group--item-content">
                        <h4>No Sites</h4>
                    </div>
                    <div class="btn-footer text-center">
                        <button class="btn">Delete</button>
                        <button class="btn">Create Site</button>
                    </div>
                </div>
                <div class="group--item">
                    <a class="add-pile">
                        <div class="group--item-content">
                            <span class="icon-layers"></span>
                        </div>
                        <div class="btn-footer text-center">
                            <button class="btn btn-primary">Add Pile</button>
                        </div>
                    </a>
                </div>
            </div>
            <textarea rows="20">
                <div class="group">
                    <div class="group--item">
                        <div class="group--item-heading">
                            <h4>
                                <div class="group--item-heading-name">PILE NAME</div>
                                <div class="action-btn">
                                    <button class="btn btn-small">
                                        <span class="icon-pencil"></span>
                                    </button>
                                </div>
                            </h4>
                        </div>

                        <div class="group--item-content">
                            <h4>Sites</h4>
                            <div class="list">
                                <a href="#" class="list--item">
                                    <div class="list--item-name">
                                        SITE NAME
                                    </div>
                                </a>
                                <a href="#" class="list--item">
                                    <div class="list--item-name">
                                        SITE NAME
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="btn-footer text-center">
                            <button class="btn">Delete</button>
                            <button class="btn">Create Site</button>
                        </div>
                    </div>

                </div>
            </textarea>
        </div>
    </div>

@endsection