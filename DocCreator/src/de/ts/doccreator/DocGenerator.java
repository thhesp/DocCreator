package de.ts.doccreator;

import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.File;
import java.io.FileWriter;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.UnsupportedEncodingException;
import java.util.ArrayList;
import java.util.Date;

import de.ts.doccreator.model.DocFile;
import de.ts.doccreator.model.DocModel;

public class DocGenerator {
	private static final String templatePath = "./de/ts/doccreator/html/templates/";
	private static final String htmlPath = "./de/ts/doccreator/html/";
	
	private DocModel model;
	private String path = ".";
	private ArrayList<DocFile> files;

	private String fileTemplate;
	private String functionTemplate;
	private String summaryTemplate;
	private String mainFrameTemplate;
	private String overviewSummaryElementTemplate;
	private String overviewFrameTemplate;
	private String overviewFrameLinkTemplate;

	public DocGenerator(DocModel model) {
		this.model = model;
		files = model.getDocFiles();
		loadTemplates();
	}

	public DocGenerator(DocModel model, String path) {
		this.model = model;
		this.path = path + "/doc/";
		files = model.getDocFiles();
		loadTemplates();
	}

	private void loadTemplates() {
		fileTemplate = readFile(templatePath + "file_template.txt");
		
		functionTemplate = readFile(templatePath + "function_template.txt");

		summaryTemplate = readFile(templatePath + "function_summary_template.txt");

		mainFrameTemplate = readFile(templatePath + "overview-summary_template.txt");

		overviewSummaryElementTemplate = readFile(templatePath + "overview_summary_single.txt");
		
		overviewFrameTemplate = readFile(templatePath + "overview-frame_template.txt");
		
		overviewFrameLinkTemplate = readFile(templatePath + "file_link_overview_frame.txt");
		
		
	}

	public void generateDoc() {
		createStructure();
		copyFiles();
		for (DocFile f : files) {
			String fileDoc = f.generateHTML(fileTemplate, functionTemplate,
					summaryTemplate);
			createFile(f.getName(), path + "files/", fileDoc);
		}
		addOverviewSummary();
		addNavigation();

	}

	// folder structure
	private void createStructure() {
		new File(path).mkdir();
		new File(path + "/files").mkdir();
	}

	private void createFile(String name, String path, String input) {
		createFile(name, path, input, "html");

	}
	
	private void createFile(String name, String path, String input, String type) {
		File file = new File(path + name + "." + type);
		try {
			file.createNewFile();
			FileWriter fstream = new FileWriter(file);
			BufferedWriter out = new BufferedWriter(fstream);
			out.write(input);
			// Close the output stream
			out.close();
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}

	}

	// Starting overview
	private void addOverviewSummary() {
		String frameLinks = "";
		for (DocFile f : files) {
			frameLinks += f.generateOverviewFrameLink(overviewFrameLinkTemplate);
		}

		String overviewFrame = overviewFrameTemplate.replace("{Filelist}",
				frameLinks);
		createFile("overview-frame", path, overviewFrame);
	}

	// Bottom left Frame for all files
	private void addNavigation() {
		String summaryElements = "";
		int count = 0;
		for (DocFile f : files) {
			String color = "rowColor";
			if (count % 2 == 0)
				color = "altColor";
			summaryElements += f.generateOverviewSummaryElement(
					overviewSummaryElementTemplate, color);
		}
		String timestamp = new Date().toString();
		String overviewSummary = mainFrameTemplate.replace("{Filesummary}",
				summaryElements);
		overviewSummary = overviewSummary.replace("{Timestamp}", timestamp);
		createFile("overview-summary", path, overviewSummary);

	}

	private void copyFiles() {
		String css = readFile(htmlPath+"stylesheet.css");
		createFile("stylesheet", path, css,"css");
		
		String index = readFile(htmlPath+"index.txt");
		createFile("index", path, index);
	}

	private String readFile(String path) {
		String filedata ="";
		try {
			ClassLoader CLDR = getClass().getClassLoader();
			InputStream inputStream = CLDR
					.getResourceAsStream(path);
			InputStreamReader in = new InputStreamReader(inputStream, "UTF-8");
			BufferedReader reader = new BufferedReader(in);
			String line = null;
			while((line = reader.readLine()) != null){
				filedata += line;
			}
			reader.close();
			in.close();
			inputStream.close();
		} catch (UnsupportedEncodingException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}

		return filedata.toString();
	}

}
