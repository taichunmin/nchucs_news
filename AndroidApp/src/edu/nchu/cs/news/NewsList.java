package edu.nchu.cs.news;

import android.os.Bundle;
import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.Menu;
import android.view.View;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;

public class NewsList extends Activity {

	private static final String ACTIVITY_TAG = "NewsList";
	private RelativeLayout rl_newsListItem1;
	private LayoutInflater inflater;
	private LinearLayout ll_newsListContent;
	private TextView tv_newsListTitle;
	private int ListType = 0; // 0=today, 1=date, 2=cateDay

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_news_list);
		findViews();
		setListeners();
		processIntent();
	}

	private void processIntent() {
		try {
			Bundle bundle = this.getIntent().getExtras();
			String ListTypeStr = null;
			if (bundle != null)
				ListTypeStr = bundle.getString("LIST_TYPE");
			if (ListTypeStr == null)
				ListTypeStr = "today";
			if (ListTypeStr.equals("date")) {
				ListType = 1;
				// tv_newsListTitle.setText( getText(R.string.list_type_today)
				// );
			} else if (ListTypeStr.equals("category")) {
				ListType = 2;
				// tv_newsListTitle.setText( getText(R.string.list_type_today)
				// );
			} else
				throw new Exception("");
		} catch (Exception e) {
			if (e.getMessage().length() != 0)
				Log.e(ACTIVITY_TAG, e.getMessage());
			tv_newsListTitle.setText(getText(R.string.list_type_today));
			ListType = 0;
		}
	}

	private void findViews() {
		rl_newsListItem1 = (RelativeLayout) findViewById(R.id.rl_newsListItem1);
		tv_newsListTitle = (TextView) findViewById(R.id.tv_newsListTitle);
		rl_newsListItem1.setTag(123);
		ll_newsListContent = (LinearLayout) findViewById(R.id.ll_newsListContent);
		inflater = (LayoutInflater) getSystemService(Context.LAYOUT_INFLATER_SERVICE);
	}

	private void setListeners() {
		rl_newsListItem1.setOnClickListener(clickViewNews);
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.news_list, menu);
		return true;
	}

	private View.OnClickListener clickViewNews = new View.OnClickListener() {
		@Override
		public void onClick(View v) {
			try {
				int nid = (Integer) v.getTag();
				Intent intent = new Intent();
				intent.setClass(NewsList.this, ViewNews.class);
				Bundle bundle = new Bundle();
				bundle.putInt("NID", nid);
				intent.putExtras(bundle);
				startActivity(intent);
			} catch (Exception e) {
				Log.e(ACTIVITY_TAG, e.toString());
			}
		}
	};

	private void addNewsListGUI(int nid) {
		View ListItemView = inflater.inflate(R.layout.new_news_list_view, null);

		TextView tv_newsItemDate = (TextView) ListItemView.findViewById(R.id.tv_newsItemDate),
				tv_newsItemTitle = (TextView) ListItemView.findViewById(R.id.tv_newsItemTitle);

		// Get Data
		
		ListItemView.setOnClickListener(clickViewNews);
		tv_newsItemTitle.setText("");
		tv_newsItemDate.setText("");

		ListItemView.setTag(nid);
	}
}
