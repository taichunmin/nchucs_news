package edu.nchu.cs.news;

import android.os.Bundle;
import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.content.res.Resources;
import android.util.DisplayMetrics;
import android.util.Log;
import android.view.ContextThemeWrapper;
import android.view.Menu;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.RadioButton;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

public class MainActivity extends Activity {

	private static final String ACTIVITY_TAG="Main";
	Button btn_view_news, btn_login, btn_news_list, btn_filter;
	LinearLayout ll_mainBtnGroup;
	int btnSquareSize = 100;
	int btnImageSize = 60;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main);
		findViews();
		setAllBtnSquare(ll_mainBtnGroup);
		setListeners();
		
		if(true)
		{
			// if login
			Toast.makeText(getApplicationContext(),"使用返回鍵回到主選單",Toast.LENGTH_SHORT).show();
			gotoNewsList(null);
		}
		else
		{
			// if no login
			Toast.makeText(getApplicationContext(),"請登入以繼續...",Toast.LENGTH_SHORT).show();
			gotoLogin();
		}
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.main, menu);
		return true;
	}
	
	private void findViews()
	{
		btn_view_news = (Button) findViewById( R.id.btn_view_news );
		btn_login = (Button) findViewById( R.id.btn_login );
		btn_news_list = (Button) findViewById( R.id.btn_news_list );
		btn_filter = (Button) findViewById( R.id.btn_filter );
		ll_mainBtnGroup = (LinearLayout) findViewById(R.id.ll_mainBtnGroup);
		
		// 取得螢幕寬度
		DisplayMetrics metrics = this.getResources().getDisplayMetrics();
		int width = metrics.widthPixels;
		int height = metrics.heightPixels;
		btnSquareSize = width;
		if(height<width)
			btnSquareSize = height;
		// Log.d(ACTIVITY_TAG, "width: "+width+", height: "+height);
		btnSquareSize -= (int) Math.ceil(convertDpToPixel(15, this));
		btnSquareSize /= 2;
		btnImageSize = (int) Math.floor(btnSquareSize * 0.6);
		Log.d(ACTIVITY_TAG, "width: "+width+", height: "+height+", btnSquareSize: "+btnSquareSize);
	}
	
	private void setListeners()
	{
		btn_view_news.setOnClickListener(listen_view_news);
		btn_login.setOnClickListener(listen_login);
		btn_news_list.setOnClickListener(listen_news_list);
		btn_filter.setOnClickListener(listen_filter);
	}
	
	private void gotoNewsList( String ListTypeStr )
	{
		if(ListTypeStr == null)
			ListTypeStr = "today";
		startActivity( new Intent().setClass(MainActivity.this, NewsList.class) );
	}
	
	private void gotoLogin()
	{
		startActivity( new Intent().setClass(MainActivity.this, LoginActivity.class) );
	}
	
	private void setAllBtnSquare(LinearLayout layout)
	{
		for (int i = 0; i < layout.getChildCount(); i++) {
	        View v = layout.getChildAt(i);
	        if (v.getClass() == RelativeLayout.class) { // v instanceof RelativeLayout
	            //validate your EditText here
	        	RelativeLayout rl = (RelativeLayout)v;
	        	rl.getLayoutParams().width=btnSquareSize;
	        	rl.getLayoutParams().height=btnSquareSize;
	        	
	        	// 設定圖片為 0.6
	        	rl.getChildAt(0).getLayoutParams().height=btnImageSize;
	        } else if (v.getClass() == LinearLayout.class) {
	            //validate RadioButton
	        	setAllBtnSquare((LinearLayout)v);
	        } //etc. If it fails anywhere, just return false.
	    }
	}

	private Button.OnClickListener listen_view_news = new Button.OnClickListener()
	{
		@Override
		public void onClick(View v) {
			startActivity( new Intent().setClass(MainActivity.this, ViewNews.class) );
		}
	};

	private Button.OnClickListener listen_login = new Button.OnClickListener()
	{
		@Override
		public void onClick(View v) {
			gotoLogin();
		}
	};

	private Button.OnClickListener listen_news_list = new Button.OnClickListener()
	{
		@Override
		public void onClick(View v) {
			gotoNewsList( null );
		}
	};

	private Button.OnClickListener listen_filter = new Button.OnClickListener()
	{
		@Override
		public void onClick(View v) {
			startActivity( new Intent().setClass(MainActivity.this, FilterActivity.class) );
		}
	};
	
	/**
	 * This method converts dp unit to equivalent pixels, depending on device density. 
	 * 
	 * @param dp A value in dp (density independent pixels) unit. Which we need to convert into pixels
	 * @param context Context to get resources and device specific display metrics
	 * @return A float value to represent px equivalent to dp depending on device density
	 */
	public static float convertDpToPixel(float dp, Context context){
	    Resources resources = context.getResources();
	    DisplayMetrics metrics = resources.getDisplayMetrics();
	    float px = dp * (metrics.densityDpi / 160f);
	    return px;
	}

	/**
	 * This method converts device specific pixels to density independent pixels.
	 * 
	 * @param px A value in px (pixels) unit. Which we need to convert into db
	 * @param context Context to get resources and device specific display metrics
	 * @return A float value to represent dp equivalent to px value
	 */
	public static float convertPixelsToDp(float px, Context context){
	    Resources resources = context.getResources();
	    DisplayMetrics metrics = resources.getDisplayMetrics();
	    float dp = px / (metrics.densityDpi / 160f);
	    return dp;
	}
	
}
