// package onlocal.testing.selenium

import de.l3s.boilerpipe.BoilerpipeProcessingException;
import de.l3s.boilerpipe.extractors.ArticleExtractor;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.firefox.FirefoxDriver;
import org.openqa.selenium.remote.DesiredCapabilities;
import org.openqa.selenium.support.ui.ExpectedCondition;
import org.openqa.selenium.support.ui.WebDriverWait;

import java.io.BufferedWriter;
import java.io.File;
import java.io.FileWriter;
import java.io.IOException;

import java.util.regex.Matcher;
import java.util.regex.Pattern;


public class Smpl03main {

    private static final String DEFAULT_SEARCH_PHRASE = new String("qa");
    private static final char SEARCH_PHRASE_DELIMETER = ' ';
    private static String searchPhrase = DEFAULT_SEARCH_PHRASE;
    private static String regexpPhrase = "(\\W|^)(" + DEFAULT_SEARCH_PHRASE + ")(\\W|$)";

    static final String marionetteDriverPath = "D:\\bin\\bySelenium\\geckodriver.exe";
    static final String contentFileName = "outBoilerpipe.txt";

    // локатор третьей ссылки в выдаче google
    private static String cssLocator = new String("#rso > div > div:nth-child(3) > div > h3 > a");

    private static WebDriver driver;

    static int countSearchPhrase = 0;

    // извлечение plain text из html-кода страницы
    // используется библиотека Boilerpipe
    // htmlPage — строка html-кода
    //
    public static String extractText(String htmlPage) {
        String sb = "_";
        try {
            sb = new String( ArticleExtractor.INSTANCE.getText(htmlPage) );
        }
        catch (BoilerpipeProcessingException e) {
            e.printStackTrace();
        }
        return sb;
    }

    // поиск количества вхождений ключевой фразы в тексте html-страницы
    // используются регулярные выражения, регистр символов не учитывается
    //
    private static int getCountSearchPhrase(String content) {
        Pattern phrase = Pattern.compile(regexpPhrase, Pattern.CASE_INSENSITIVE);
        Matcher matchStr = phrase.matcher(content);
        int t = 0;
        while (matchStr.find())
            t++;
        return t;                   // countSearchPhrase;
    }

    // запись содержимого html-страницы в файл
    //
    public static void writeInFile(String htmlContent) {
        File outFile = new File(contentFileName);
        BufferedWriter outFileW;
        try {
            if (!outFile.exists())
                outFile.createNewFile();

            outFileW = new BufferedWriter(new FileWriter(outFile.getAbsoluteFile()));
            try {
                outFileW.write(htmlContent);
            } finally {
                outFileW.close();
            }
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    public static void main(String[] args) {

        System.setProperty("webdriver.gecko.driver", marionetteDriverPath);
        DesiredCapabilities capabilities = DesiredCapabilities.firefox();
        capabilities.setCapability("marionette", true);
        driver = new FirefoxDriver(capabilities);

        driver.get("https://www.google.ru");

        // ожидание; нужно время, чтобы заполнить капчу
        //
        (new WebDriverWait(driver, 10)).until(new ExpectedCondition<Boolean>() {
            public Boolean apply(WebDriver d) {
                return d.findElement(By.name("q")).isDisplayed();
            }
        });
        WebElement element = driver.findElement(By.name("q"));

        // формирование строки поискового запроса из аргументов программы
        //
        if (args.length > 0) {
            StringBuilder srchPhraseSb = new StringBuilder();
            StringBuilder regexpPhraseSb = new StringBuilder();

            for (String s : args) {
                srchPhraseSb.append(s);
                srchPhraseSb.append(SEARCH_PHRASE_DELIMETER);

                regexpPhraseSb.append(s);
                regexpPhraseSb.append('|');
            }
            searchPhrase = srchPhraseSb.toString().trim();
            regexpPhrase = regexpPhraseSb.substring(0, regexpPhraseSb.length()-1).toString();
            regexpPhrase = "(\\W|^)(" + regexpPhrase + ")(\\W|$)";
        }
//System.out.println("regexpPhrase is: \n" + regexpPhrase);

        element.sendKeys(searchPhrase);
        element.submit();

        (new WebDriverWait(driver, 10)).until(new ExpectedCondition<Boolean>() {
            public Boolean apply(WebDriver d) {
                return d.findElement(By.cssSelector(cssLocator)).isDisplayed();
            }
        });

        // искомый элемент в DOM-дереве
        WebElement srchEl = driver.findElement(By.cssSelector(cssLocator));

        String srchHref = srchEl.getAttribute("href");
//System.out.println(srchHref);

        // перезапуск драйвера с новым url
        driver.quit();
        driver = new FirefoxDriver(capabilities);
        driver.get(srchHref);

        // TODO проверки, отсечь 5xx ошибки, подгрузить ajax
        //
        String htmlContentAsString = extractText( driver.getPageSource() );

//System.out.println(htmlContentAsString);
//writeInFile(htmlContentAsString);

        countSearchPhrase = getCountSearchPhrase(htmlContentAsString);
        driver.quit();

        if (countSearchPhrase == 0)
            System.out.println("\nFailed\n");
        else
            System.out.println("\nPassed\n");
    }
}
