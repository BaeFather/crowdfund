/*
 * Post Setup
 */
$.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="_csrf"]').attr('content') }
});
