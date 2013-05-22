package edu.nchu.cs.news;

import android.os.Bundle;
import android.app.Activity;
import android.app.AlertDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.res.Resources;
import android.util.DisplayMetrics;
import android.util.Log;
import android.view.Menu;
import android.view.View;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.Toast;

public class MainActivity extends Activity {

	private static final String ACTIVITY_TAG = "Main";
	Button btn_view_news, btn_login, btn_filter;
	LinearLayout ll_mainBtnGroup;
	RelativeLayout rl_btnToday, rl_btnAbout, rl_btnDateFliter,
			rl_btnCategoryFilter, rl_btnSetting, rl_btnLogout;
	int btnSquareSize = 100;
	int btnImageSize = 60;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main);
		findViews();
		setAllBtnSquare(ll_mainBtnGroup);
		setListeners();

		if (false) {
			// if login
			Toast.makeText(getApplicationContext(), "使用返回鍵回到主選單",
					Toast.LENGTH_SHORT).show();
			// 切換至今日推薦
			startActivity(new Intent().setClass(MainActivity.this,
					NewsList.class));
		} else {
			// if no login
			Toast.makeText(getApplicationContext(), "請登入以繼續...",
					Toast.LENGTH_SHORT).show();
			gotoLogin();
		}
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.main, menu);
		return true;
	}

	private void findViews() {
		btn_view_news = (Button) findViewById(R.id.btn_view_news);
		btn_login = (Button) findViewById(R.id.btn_login);
		btn_filter = (Button) findViewById(R.id.btn_filter);
		ll_mainBtnGroup = (LinearLayout) findViewById(R.id.ll_mainBtnGroup);
		rl_btnToday = (RelativeLayout) findViewById(R.id.rl_btnToday);
		rl_btnAbout = (RelativeLayout) findViewById(R.id.rl_btnAbout);
		rl_btnDateFliter = (RelativeLayout) findViewById(R.id.rl_btnDateFliter);
		rl_btnCategoryFilter = (RelativeLayout) findViewById(R.id.rl_btnCategoryFilter);
		rl_btnSetting = (RelativeLayout) findViewById(R.id.rl_btnSetting);
		rl_btnLogout = (RelativeLayout) findViewById(R.id.rl_btnLogout);

		// 取得螢幕寬度
		DisplayMetrics metrics = this.getResources().getDisplayMetrics();
		int width = metrics.widthPixels;
		int height = metrics.heightPixels;
		btnSquareSize = width;
		if (height < width)
			btnSquareSize = height;
		// Log.d(ACTIVITY_TAG, "width: "+width+", height: "+height);
		btnSquareSize -= (int) Math.ceil(convertDpToPixel(15, this));
		btnSquareSize /= 2;
		btnImageSize = (int) Math.floor(btnSquareSize * 0.6);
		Log.d(ACTIVITY_TAG, "width: " + width + ", height: " + height
				+ ", btnSquareSize: " + btnSquareSize);
	}

	private void setListeners() {
		btn_view_news.setOnClickListener(listen_view_news);
		btn_login.setOnClickListener(listen_login);
		btn_filter.setOnClickListener(listen_filter);
		rl_btnToday.setOnClickListener(listen_btnToday);
		// rl_btnAbout.setOnClickListener(listen_btnAbout);
		rl_btnDateFliter.setOnClickListener(listen_btnDateFliter);
		rl_btnCategoryFilter.setOnClickListener(listen_btnCategoryFilter);
		// rl_btnSetting.setOnClickListener(listen_btnSetting);
		rl_btnLogout.setOnClickListener(listen_btnLogout);
	}

	private void gotoLogin() {
		startActivity(new Intent().setClass(MainActivity.this,
				LoginActivity.class));
	}

	private void setAllBtnSquare(LinearLayout layout) {
		for (int i = 0; i < layout.getChildCount(); i++) {
			View v = layout.getChildAt(i);
			if (v.getClass() == RelativeLayout.class) { // v instanceof
														// RelativeLayout
				RelativeLayout rl = (RelativeLayout) v;
				rl.getLayoutParams().width = btnSquareSize;
				rl.getLayoutParams().height = btnSquareSize;

				// 設定圖片為60%
				rl.getChildAt(0).getLayoutParams().height = btnImageSize;
			} else if (v.getClass() == LinearLayout.class) {
				setAllBtnSquare((LinearLayout) v);
			}
		}
	}

	private Button.OnClickListener listen_view_news = new Button.OnClickListener() {
		@Override
		public void onClick(View v) {
			startActivity(new Intent().setClass(MainActivity.this,
					ViewNews.class));
		}
	};

	private Button.OnClickListener listen_login = new Button.OnClickListener() {
		@Override
		public void onClick(View v) {
			gotoLogin();
		}
	};

	private Button.OnClickListener listen_filter = new Button.OnClickListener() {
		@Override
		public void onClick(View v) {
			startActivity(new Intent().setClass(MainActivity.this,
					FilterActivity.class));
		}
	};

	private View.OnClickListener listen_btnToday = new View.OnClickListener() {
		@Override
		public void onClick(View v) {
			startActivity(new Intent().setClass(MainActivity.this,
					NewsList.class));
		}
	};

	private View.OnClickListener listen_btnDateFliter = new View.OnClickListener() {
		@Override
		public void onClick(View v) {
			Bundle bundle = new Bundle();
			bundle.putString("FILTER", "date");
			startActivity(new Intent().setClass(MainActivity.this,
					FilterActivity.class).putExtras(bundle));
		}
	};

	private View.OnClickListener listen_btnCategoryFilter = new View.OnClickListener() {
		@Override
		public void onClick(View v) {
			Bundle bundle = new Bundle();
			bundle.putString("FILTER", "rid");
			startActivity(new Intent().setClass(MainActivity.this,
					FilterActivity.class).putExtras(bundle));
		}
	};

	private View.OnClickListener listen_btnLogout = new View.OnClickListener() {
		@Override
		public void onClick(View v) {

			AlertDialog.Builder builder = new AlertDialog.Builder(
					MainActivity.this);
			builder.setTitle(R.string.logoutConfirm);
			builder.setPositiveButton(android.R.string.yes,
					new DialogInterface.OnClickListener() {

						@Override
						public void onClick(DialogInterface dialog, int which) {
							startActivity(new Intent().setClass(
									MainActivity.this, LoginActivity.class));
						}
					});
			builder.setCancelable(true);
			builder.setNegativeButton(android.R.string.no, null);
			
			builder.setMessage(R.string.confirmLogoutMessage);
			
			AlertDialog confirmDialog = builder.create();
			confirmDialog.show();
		}
	};

	/**
	 * This method converts dp unit to equivalent pixels, depending on device
	 * density.
	 * 
	 * @param dp
	 *            A value in dp (density independent pixels) unit. Which we need
	 *            to convert into pixels
	 * @param context
	 *            Context to get resources and device specific display metrics
	 * @return A float value to represent px equivalent to dp depending on
	 *         device density
	 */
	public static float convertDpToPixel(float dp, Context context) {
		Resources resources = context.getResources();
		DisplayMetrics metrics = resources.getDisplayMetrics();
		float px = dp * (metrics.densityDpi / 160f);
		return px;
	}

	/**
	 * This method converts device specific pixels to density independent
	 * pixels.
	 * 
	 * @param px
	 *            A value in px (pixels) unit. Which we need to convert into db
	 * @param context
	 *            Context to get resources and device specific display metrics
	 * @return A float value to represent dp equivalent to px value
	 */
	public static float convertPixelsToDp(float px, Context context) {
		Resources resources = context.getResources();
		DisplayMetrics metrics = resources.getDisplayMetrics();
		float dp = px / (metrics.densityDpi / 160f);
		return dp;
	}
}
