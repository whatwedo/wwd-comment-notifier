# WWD Comment Notifier
Developed with ♥ by whatwedo(https://whatwedo.ch) in Bern

> Note: This plugin was initially written in 2019 and used on a production site for 6 years.
> The client doesn't need the functionality anymore, but we thought it could still be useful or an inspiration for some people.
>
> We don't maintain this code for future changes, but it was working up the following system requirements as of May 2025:
> - WordPress 6.8
> - PHP 8.4

## Config
The plugin is shipped without any option page in WordPress. To configure the plugin use the JSON file in the config folder. This is a stateful and developer friendly way to customize the plugin.

Copy the config file within the `config` directory and adjust the content for your preferences.
Add the custom config to your theme and use the following hook to register it:

```
add_filter('wwdcn_config_path', function () {
    return get_template_directory() . '/config/custom-notifier-config.json';
});
```


### Variables

Defined variables in JSON file are not optional.

1. name (string)
1. mail (object)
  1. subject (string)
  1. content (string: Multilines are solved with an array structure [])
  1. from_address (string)
  1. excerpt_length (int)
1. checkbox (object)
  1. label (sting)
  1. default_state (int)
1. unsubscribe (object)
  1. title (string)
  1. text (string)
  1. url (string: `/` = homepage, false = subscriped post url)
  1. redirect_url (bol/string: false = dies and then redirects, set custom url here if you want to handle it differently)
  1. redirect_time (bol/string: just works when `redirect_url` is set to false)

### Placeholders
Inside the mail content there are some magic placeholders available which will be turned into a dynamic value.

#### General placeholders:
- **{post_name}**: The title of the post
- **{post_link}**: The link to the post
- **{post_author}**: The name of the author which wrote the post
- **{comment_excerpt}**: An excerpt with the specified `excerpt_length` of the comment
- **{comment_link}**: Specific link to the comment of the post
- **{comment_time}**: The time of the comment (date + time) in ...
- **{comment_author}**: The author of the comment

#### Personalized placeholders:
- **{name}**: Name of the subscriber
- **{unsubscribe_link}**: The link to unsubscribe from current post


