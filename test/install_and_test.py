
import os
import time
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC

def test_plugin_installation():
    # 環境変数を取得
    wp_site = os.getenv('WPSITE')
    wp_login = os.getenv('WPLOGIN')
    wp_user = os.getenv('WPUSER')
    wp_password = os.getenv('WPPASSWORD')
    
    if not all([wp_site, wp_login, wp_user, wp_password]):
        raise Exception("Required environment variables are not set")

    # Chromeドライバーの設定
    options = webdriver.ChromeOptions()
    options.add_argument('--headless')
    options.add_argument('--no-sandbox')
    driver = webdriver.Chrome(options=options)
    
    try:
        # WordPressにログイン
        print("Logging into WordPress...")
        driver.get(wp_login)
        
        username = driver.find_element(By.ID, "user_login")
        password = driver.find_element(By.ID, "user_pass")
        
        username.send_keys(wp_user)
        password.send_keys(wp_password)
        
        driver.find_element(By.ID, "wp-submit").click()
        
        # プラグインをアップロード
        print("Uploading plugin...")
        driver.get(f"{wp_site}/wp-admin/plugin-install.php?tab=upload")
        
        file_input = driver.find_element(By.ID, "pluginzip")
        file_input.send_keys(os.path.abspath("../llms-txt-full-txt-generator.zip"))
        
        driver.find_element(By.ID, "install-plugin-submit").click()
        
        # プラグインを有効化
        print("Activating plugin...")
        WebDriverWait(driver, 10).until(
            EC.presence_of_element_located((By.XPATH, "//a[contains(@class, 'activate-now')]"))
        ).click()
        
        # 設定ページに移動
        print("Configuring plugin...")
        driver.get(f"{wp_site}/wp-admin/options-general.php?page=llms-txt-generator")
        
        # 投稿タイプを選択
        post_checkbox = WebDriverWait(driver, 10).until(
            EC.presence_of_element_located((By.XPATH, "//input[@type='checkbox'][@value='post']"))
        )
        if not post_checkbox.is_selected():
            post_checkbox.click()
            
        # 設定を保存
        driver.find_element(By.NAME, "submit").click()
        
        # ファイル生成
        print("Generating files...")
        driver.find_element(By.NAME, "generate_llms_txt").click()
        
        # ファイルの存在確認
        time.sleep(2)
        driver.get(f"{wp_site}/llms.txt")
        llms_txt_content = driver.page_source
        
        driver.get(f"{wp_site}/llms-full.txt")
        llms_full_txt_content = driver.page_source
        
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
        driver.quit()

if __name__ == "__main__":
    test_plugin_installation()
