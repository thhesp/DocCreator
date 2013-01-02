package de.ts.doccreator;

import java.util.ArrayList;

import de.ts.doccreator.model.DocFunction;

public class FileParser {
	
	private String content;
	private String fileComment;
	private String filename;
	private ArrayList<String> functions;
	private DocFunction[] docFunctions;
	
	public FileParser(String content, String filename){
		this.content = content;
		this.filename = filename;
		functions = new ArrayList<String>();
		extract();
		docFunctions = new DocFunction[functions.size()];
		generateDocFunctions();
	}
	
	private void extract(){
		String tail = extractFileComment();
		extractFunction(tail);
	}

	private String extractFileComment() {
		if(content.indexOf("/**") >= 0){
			int start = content.indexOf("/**");
			int end = content.indexOf("**/");
			end += 3;
			fileComment = content.substring(start, end);
			if(fileComment.contains("@lang:")){
				fileComment = "";
				return content;
			}
			return content.substring(end);
		}
		return content;
	}

	private void extractFunction(String tail) {
		if(tail.indexOf("/**") >= 0){
			int start = tail.indexOf("/**");
			int end = tail.indexOf("**/");
			end += 3;
			System.out.println("Substring start: " + start +" and end: "+end);
			functions.add(tail.substring(start, end));
			extractFunction(tail.substring(end));
		}
		
	}
	
	private void generateDocFunctions(){
		int position = 0;
		for(String s: functions){
			docFunctions[position] = generateDocFunction(s);
			position++;
		}
	}
	
	private DocFunction generateDocFunction(String function){
		DocFunction func = new DocFunction(extractFunctionName(function));
		func.setFilename(filename);
		func.setType(extractFunctionType(function));
		func.setDescription(extractFunctionDescription(function));
		func.setLanguage(extractFunctionLanguage(function));
		func.setReturnType(extractFunctionReturnType(function));
		
		ArrayList<String[]> parameters = extractFunctionParameters(function);
		for(String[] s: parameters){
			func.addParameters(s[0], s[1]);
		}
		
		return func;
	}
	
	private String extractFunctionName(String function){
		// @name: Name;
		
		int start = function.indexOf("@name");
		int end = function.indexOf(";", start);
		
		start+= 6;
	
		return function.substring(start, end);
	}
	
	private String extractFunctionDescription(String function){
		// @desc: Description;
		
		int start = function.indexOf("@desc");
		int end = function.indexOf(";", start);
		
		start+= 6;
	
		return function.substring(start, end);
	}
	
	private String extractFunctionType(String function){
		// @type: type;
		// can be php, js, java
		
		int start = function.indexOf("@type");
		int end = function.indexOf(";", start);
		
		start+= 6;
	
		return function.substring(start, end);
	}
	
	private String extractFunctionLanguage(String function){
		// @lang: Name;
		
		int start = function.indexOf("@lang");
		int end = function.indexOf(";", start);
		
		start+= 6;
	
		return function.substring(start, end);
	}
	
	private ArrayList<String[]> extractFunctionParameters(String function){
		// @param: paramtype paramname;
		// @param: paramtype paramname;
		// @param: paramtype paramname;
		// @param: paramtype paramname;
		int start = 0;
		int end = 0;
		ArrayList<String[]> parameters = new ArrayList<String[]>();
		
		while(function.indexOf("@param", end) != -1){
			start = function.indexOf("@param");
			end =  function.indexOf(";", start);
			start += 7;
			String substring = function.substring(start, end);
			substring = substring.trim();
			parameters.add(substring.split(" "));
		}
		
		return parameters;
	}
	
	private String extractFunctionReturnType(String function){
		// @return: type;
		
		int start = function.indexOf("@return");
		int end = function.indexOf(";", start);
		
		start+= 8;
	
		return function.substring(start, end);
	}
	
	public String getFileDescription() {
		// @desc: Description;
		
		int start = fileComment.indexOf("@desc");
		int end = fileComment.indexOf(";", start);
		
		start+= 6;
	
		return fileComment.substring(start, end);
	}

	public DocFunction[] getFunctions() {
		return docFunctions;
	}
	
	

}
