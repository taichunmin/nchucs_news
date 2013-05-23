package edu.nchu.cs.news;

import android.os.Bundle;
import android.app.Activity;
import android.util.Log;
import android.view.Menu;
import android.widget.TextView;

public class FilterActivity extends Activity {

	TextView tv_filterTitle;
	private final String ACTIVITY_TAG = "Filter";
	boolean filterRid = false;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_filter);
		findViews();
		setListeners();
		processIntent();
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.filter, menu);
		return true;
	}

	private void findViews() {
		tv_filterTitle = (TextView) findViewById(R.id.tv_filterTitle);
	}

	private void setListeners() {

	}

	private void processIntent() {

		try {
			Bundle bundle = this.getIntent().getExtras();
			if (bundle == null)
				throw new Exception("");
			if (bundle.getString("FILTER").equals("rid"))
				filterRid = true;
		} catch (Exception e) {
			if (e.getMessage().length() != 0)
				Log.e(ACTIVITY_TAG, e.getMessage());
			filterRid = false;
		}
		if (filterRid)
			tv_filterTitle.setText(getText(R.string.filter_rid));
		else
			tv_filterTitle.setText(getText(R.string.filter_date));
	}
}
