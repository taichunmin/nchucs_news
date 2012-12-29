package edu.nchu.cs.dmlab.newsrecommender;

import android.os.Bundle;
import android.app.Activity;
import android.content.pm.ActivityInfo;
import android.text.format.Time;
import android.util.Log;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.webkit.WebViewClient;

public class SystemMain extends Activity {
	private WebView webview;
	private final String url = "http://140.120.15.141";
	/* (non-Javadoc)
	 * @see android.app.Activity#onStop()
	 */
	@Override
	protected void onStop() {
		// TODO Auto-generated method stub
		
		Time time = new Time();
        time.setToNow();
        Log.i("Main", url +"/index.php?act=logout&time="+time.format("%Y%m%d%H%M%S"));
		webview.loadUrl(url +"/index.php?act=logout&time="+time.format("%Y%m%d%H%M%S"));
		
		super.onStop();
	}

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_PORTRAIT);
//        CookieSyncManager.createInstance(this);
//        CookieSyncManager.getInstance().startSync();

        webview = new WebView(this);
        WebSettings webSettings = webview.getSettings();  
        webSettings.setJavaScriptEnabled(true);
        webview.clearCache(true);
        webview.clearHistory();
        webSettings.setCacheMode(WebSettings.LOAD_NO_CACHE);
//        webSettings.setJavaScriptCanOpenWindowsAutomatically(true);
//        webSettings.setCacheMode(WebSettings.LOAD_DEFAULT);
        webview.setWebViewClient(new Callback());
        
        Time time = new Time();
        time.setToNow();
        webview.loadUrl(url +"?time="+time.format("%Y%m%d%H%M%S"));
        setContentView(webview);
    }
    private class Callback extends WebViewClient{  //HERE IS THE MAIN CHANGE.
    	@Override
        public boolean shouldOverrideUrlLoading(WebView view, String url) {
        	Log.i("Check", url);
        	view.loadUrl(url);
            return true;
        }
    }
    
    
}
