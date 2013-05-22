typedef

MAP HashMap<String, String>
MAP_LIST ArrayList MAP

public:

MAP_LIST newslist_today()
MAP_LIST newslist_day()
MAP_LIST newslist_cate_today()
MAP_LIST cnt_day()
MAP_LIST cnt_cate()
void cache_today_news()
MAP system_argu(String index)
MAP get_news(String id)

private:

MAP_LIST get_list(String url)
MAP chche_news(String id)
void del_cache_over_time()
JSONObject fetch_srv_files(String url)
String get_uri_content(url)