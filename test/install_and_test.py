
import os
import time
from playwright.sync_api import sync_playwright

def test_plugin_installation():
    # 環境変数を取得
    wp_site = os.getenv('WPSITE')
    wp_login = os.getenv('WPLOGIN')
    wp_user = os.getenv('WPUSER')
    wp_password = os.getenv('WPPASSWORD')
    
    if not all([wp_site, wp_login, wp_user, wp_password]):
        raise Exception("Required environment variables are not set")

    with sync_playwright() as p:
        # ブラウザを起動
        browser = p.chromium.launch(headless=True)
        context = browser.new_context()
        page = context.new_page()
        
        try:
            # WordPressにログイン
            print("Logging into WordPress...")
            page.goto(wp_login)
            
            page.fill("#user_login", wp_user)
            page.fill("#user_pass", wp_password)
            page.click("#wp-submit")
            
            # プラグインをアップロード
            print("Uploading plugin...")
            page.goto(f"{wp_site}/wp-admin/plugin-install.php?tab=upload")
            
            with page.expect_file_chooser() as fc_info:
                page.click("#pluginzip")
            file_chooser = fc_info.value
            file_chooser.set_files(os.path.abspath("../wp-llms-txt-generator.zip"))
            
            page.click("#install-plugin-submit")
            
            # プラグインを有効化
            print("Activating plugin...")
            page.wait_for_selector("//a[contains(@class, 'activate-now')]")
            page.click("//a[contains(@class, 'activate-now')]")
            
            # 設定ページに移動
            print("Configuring plugin...")
            page.goto(f"{wp_site}/wp-admin/options-general.php?page=llms-txt-generator")
            
            # 投稿タイプを選択
            post_checkbox = page.wait_for_selector("//input[@type='checkbox'][@value='post']")
            if not post_checkbox.is_checked():
                post_checkbox.click()
                
            # 設定を保存
            page.click("input[name='submit']")
            
            # ファイル生成
            print("Generating files...")
            page.click("input[name='generate_llms_txt']")
            
            # ファイルの存在確認
            time.sleep(2)
            
            # llms.txtの確認
            page.goto(f"{wp_site}/llms.txt")
            llms_txt_content = page.content()
            
            # llms-full.txtの確認
            page.goto(f"{wp_site}/llms-full.txt")
            llms_full_txt_content = page.content()
            
            # 文字化けチェック
            if "�" in llms_txt_content or "�" in llms_full_txt_content:
                print("Warning: Character encoding issues detected")
            else:
                print("Character encoding looks good")
                
            print("Test completed successfully!")
            
        except Exception as e:
            print(f"Error during testing: {str(e)}")
            raise
            
        finally:
            browser.close()

if __name__ == "__main__":
    test_plugin_installation()
